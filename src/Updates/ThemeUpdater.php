<?php
/**
 * Theme updater.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Updates;

use ThemeZee\Amalfi\Core\SettingsRepository;
use ThemeZee\Amalfi\Core\ThemeConfig;
use stdClass;

/**
 * Checks the ThemeZee store (Easy Digital Downloads Software Licensing) for
 * theme updates and feeds them into the WordPress update system.
 *
 * This is an object-oriented reimplementation of the EDD SL theme updater:
 * its configuration comes from {@see ThemeConfig} and the license key from
 * {@see SettingsRepository} rather than from a loose arguments array.
 */
class ThemeUpdater {

	/**
	 * How long a successful version response is cached, in seconds.
	 */
	private const CACHE_SUCCESS = 12 * HOUR_IN_SECONDS;

	/**
	 * How long a failed version response is cached before retrying, in seconds.
	 */
	private const CACHE_FAILURE = 30 * MINUTE_IN_SECONDS;

	/**
	 * The transient key the version response is cached under.
	 *
	 * @var string
	 */
	private readonly string $responseKey;

	/**
	 * Constructor.
	 *
	 * @param ThemeConfig        $config   The theme configuration.
	 * @param SettingsRepository $settings The settings repository.
	 */
	public function __construct(
		private readonly ThemeConfig $config,
		private readonly SettingsRepository $settings,
	) {
		$this->responseKey = $this->config->slug() . '-update-response';
	}

	/**
	 * Register the updater hooks.
	 */
	public function register(): void {
		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'injectUpdate' ) );
		add_filter( 'delete_site_transient_update_themes', array( $this, 'flush' ) );
		add_action( 'load-update-core.php', array( $this, 'flush' ) );
		add_action( 'load-themes.php', array( $this, 'flush' ) );
		add_action( 'load-themes.php', array( $this, 'onThemesScreen' ) );
	}

	/**
	 * Add the update details to the themes update transient.
	 *
	 * @param mixed $transient The themes update transient.
	 * @return mixed
	 */
	public function injectUpdate( $transient ) {
		$update = $this->fetchVersion();

		if ( null === $update ) {
			return $transient;
		}

		if ( ! is_object( $transient ) ) {
			$transient = new stdClass();
		}

		$update['theme'] = $this->config->slug();

		if ( version_compare( $this->config->version(), $update['new_version'], '<' ) ) {
			$transient->response[ $this->config->slug() ] = $update;
		} else {
			$transient->no_update[ $this->config->slug() ] = $update;
		}

		return $transient;
	}

	/**
	 * Delete the cached version response.
	 */
	public function flush(): void {
		delete_transient( $this->responseKey );
	}

	/**
	 * Prepare the themes screen update notice.
	 */
	public function onThemesScreen(): void {
		add_thickbox();
		add_action( 'admin_notices', array( $this, 'renderUpdateNag' ) );
	}

	/**
	 * Render the update notification with a changelog thickbox.
	 */
	public function renderUpdateNag(): void {
		$response = get_transient( $this->responseKey );

		if ( ! is_object( $response ) || empty( $response->new_version ) ) {
			return;
		}

		if ( ! version_compare( $this->config->version(), $response->new_version, '<' ) ) {
			return;
		}

		$slug      = $this->config->slug();
		$theme     = wp_get_theme( $slug );
		$updateUrl = wp_nonce_url(
			'update.php?action=upgrade-theme&theme=' . rawurlencode( $slug ),
			'upgrade-theme_' . $slug
		);
		$confirm   = esc_js( __( 'Updating this theme will override all theme files. Click "Cancel" to stop, "OK" to update.', 'amalfi' ) );
		$changelog = isset( $response->sections['changelog'] ) ? (string) $response->sections['changelog'] : '';

		echo '<div id="update-nag" class="update-nag notice notice-warning inline"><p>';
		printf(
			/* translators: %1$s: theme name. %2$s: new version. %3$s: changelog link. %4$s: update link. */
			wp_kses_post( __( 'Version %2$s of %1$s is available. <a href="%3$s" class="thickbox">View changelog</a> or <a href="%4$s" onclick="%5$s">update now</a>.', 'amalfi' ) ),
			esc_html( $theme->get( 'Name' ) ),
			esc_html( $response->new_version ),
			esc_url( '#TB_inline?width=640&inlineId=' . $slug . '_changelog' ),
			esc_url( $updateUrl ),
			// The dynamic message is escaped with esc_js() above; the rest is static JS scaffolding.
			'if ( confirm(\'' . $confirm . '\') ) { return true; } return false;' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
		echo '</p></div>';

		printf(
			'<div id="%1$s_changelog" style="display:none;">%2$s</div>',
			esc_attr( $slug ),
			wp_kses_post( wpautop( $changelog ) )
		);
	}

	/**
	 * Fetch the latest version information, using the cached response when fresh.
	 *
	 * @return array<string, mixed>|null The update data, or null when unavailable.
	 */
	private function fetchVersion(): ?array {
		$cached = get_transient( $this->responseKey );

		if ( is_object( $cached ) ) {
			return (array) $cached;
		}

		$response = wp_remote_post(
			$this->config->storeUrl(),
			array(
				'timeout' => 15,
				'body'    => array(
					'edd_action'  => 'get_version',
					'license'     => $this->settings->licenseKey(),
					'name'        => $this->config->name(),
					'slug'        => $this->config->slug(),
					'version'     => $this->config->version(),
					'author'      => $this->config->author(),
					'item_id'     => $this->config->itemId(),
					'php_version' => phpversion(),
					'wp_version'  => get_bloginfo( 'version' ),
				),
			)
		);

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return $this->cacheFailure();
		}

		$data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( ! is_object( $data ) ) {
			return $this->cacheFailure();
		}

		if ( isset( $data->sections ) ) {
			$data->sections = maybe_unserialize( $data->sections );
		}

		set_transient( $this->responseKey, $data, self::CACHE_SUCCESS );

		return (array) $data;
	}

	/**
	 * Cache a short-lived failure marker and signal that no update is available.
	 *
	 * @return null
	 */
	private function cacheFailure() {
		$data              = new stdClass();
		$data->new_version = $this->config->version();

		set_transient( $this->responseKey, $data, self::CACHE_FAILURE );

		return null;
	}
}
