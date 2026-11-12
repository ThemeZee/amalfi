<?php
/**
 * Theme license management.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Admin;

/**
 * Activates and deactivates the theme license against the ThemeZee store
 * (Easy Digital Downloads Software Licensing).
 *
 * The activate/deactivate operations return a structured result so they can be
 * driven from a REST endpoint; {@see self::state()} provides the current
 * license state for bootstrapping the admin UI.
 */
class LicenseManager {

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
	}

	/**
	 * The current license state.
	 *
	 * @return array{active: bool, status: string, keyMasked: string}
	 */
	public function state(): array {
		$key = $this->settings->licenseKey();

		return array(
			'active'    => $this->settings->hasValidLicense(),
			'status'    => $this->settings->licenseStatus(),
			'keyMasked' => '' !== $key ? $this->maskKey( $key ) : '',
		);
	}

	/**
	 * Activate a license key against the store.
	 *
	 * @param string $key The license key.
	 * @return array{active: bool, status: string, keyMasked: string, message: string, type: string}
	 */
	public function activate( string $key ): array {
		$key = trim( $key );

		if ( '' === $key ) {
			return $this->result( __( 'Please enter a license key.', 'amalfi' ), 'error' );
		}

		$data = $this->request( 'activate_license', $key );

		if ( null === $data ) {
			return $this->result( __( 'An error occurred, please try again.', 'amalfi' ), 'error' );
		}

		if ( empty( $data->success ) ) {
			return $this->result( $this->errorMessage( $data ), 'error' );
		}

		$this->settings->storeLicense( $key, SettingsRepository::STATUS_VALID );

		return $this->result( __( 'License was successfully activated.', 'amalfi' ), 'success' );
	}

	/**
	 * Deactivate the stored license.
	 *
	 * @return array{active: bool, status: string, keyMasked: string, message: string, type: string}
	 */
	public function deactivate(): array {
		$key = $this->settings->licenseKey();

		if ( '' !== $key ) {
			$this->request( 'deactivate_license', $key );
		}

		$this->settings->clearLicense();

		return $this->result( __( 'License was deactivated.', 'amalfi' ), 'success' );
	}

	/**
	 * Show a reminder notice when the license is not active.
	 */
	public function renderReminder(): void {
		global $pagenow;

		if ( $this->settings->hasValidLicense() ) {
			return;
		}

		if ( ! current_user_can( $this->config->capability() ) ) {
			return;
		}

		$screens = array( 'index.php', 'update-core.php', 'themes.php' );
		if ( ! in_array( $pagenow, $screens, true ) || isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		printf(
			'<div class="notice notice-info"><p>%s</p></div>',
			wp_kses_post(
				sprintf(
					/* translators: %1$s: Theme name. %2$s: URL of the theme settings page. */
					__( 'Please enter your license key for the %1$s theme in order to receive updates and support. <a href="%2$s">Enter License Key</a>', 'amalfi' ),
					esc_html( $this->config->name() ),
					esc_url( $this->config->pageUrl() )
				)
			)
		);
	}

	/**
	 * Remove this theme from wp.org update checks so updates come from the store.
	 *
	 * @param array<string, mixed> $args The HTTP request arguments.
	 * @param string               $url  The request URL.
	 * @return array<string, mixed>
	 */
	public function disableWporgRequest( array $args, string $url ): array {
		if ( ! str_starts_with( $url, 'https://api.wordpress.org/themes/update-check/1.1/' ) ) {
			return $args;
		}

		if ( empty( $args['body']['themes'] ) ) {
			return $args;
		}

		$themes = json_decode( $args['body']['themes'] );

		if ( ! isset( $themes->themes ) ) {
			return $args;
		}

		$parent = get_option( 'template' );
		$child  = get_option( 'stylesheet' );
		unset( $themes->themes->$parent, $themes->themes->$child );

		$args['body']['themes'] = wp_json_encode( $themes );

		return $args;
	}

	/**
	 * Build a result array from the current state plus a message.
	 *
	 * @param string $message The result message.
	 * @param string $type    The result type (success / error).
	 * @return array{active: bool, status: string, keyMasked: string, message: string, type: string}
	 */
	private function result( string $message, string $type ): array {
		return array_merge(
			$this->state(),
			array(
				'message' => $message,
				'type'    => $type,
			)
		);
	}

	/**
	 * Send a licensing request to the store API.
	 *
	 * @param string $action The EDD license action (activate_license / deactivate_license).
	 * @param string $key    The license key.
	 * @return object|null The decoded response, or null on transport error.
	 */
	private function request( string $action, string $key ): ?object {
		$response = wp_remote_post(
			$this->config->storeUrl(),
			array(
				'timeout'   => 15,
				'sslverify' => true,
				'body'      => array(
					'edd_action'  => $action,
					'license'     => $key,
					'item_id'     => $this->config->itemId(),
					'item_name'   => rawurlencode( $this->config->name() ),
					'url'         => home_url(),
					'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
				),
			)
		);

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return null;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ) );

		return is_object( $data ) ? $data : null;
	}

	/**
	 * Translate an EDD error response into a human readable message.
	 *
	 * @param object $data The decoded API response.
	 */
	private function errorMessage( object $data ): string {
		$error = $data->error ?? '';

		switch ( $error ) {
			case 'expired':
				return sprintf(
					/* translators: %s: license expiration date. */
					esc_html__( 'Your license key expired on %s.', 'amalfi' ),
					isset( $data->expires )
						? esc_html( date_i18n( get_option( 'date_format' ), strtotime( $data->expires, time() ) ) )
						: ''
				);

			case 'disabled':
			case 'revoked':
				return esc_html__( 'Your license key has been disabled.', 'amalfi' );

			case 'missing':
				return esc_html__( 'Invalid license key.', 'amalfi' );

			case 'invalid':
			case 'site_inactive':
				return esc_html__( 'Your license is not active for this URL.', 'amalfi' );

			case 'item_name_mismatch':
				return sprintf(
					/* translators: %s: theme name. */
					esc_html__( 'This appears to be an invalid license key for %s.', 'amalfi' ),
					esc_html( $this->config->name() )
				);

			case 'no_activations_left':
				return esc_html__( 'Your license key has reached its activation limit.', 'amalfi' );

			default:
				return esc_html__( 'An error occurred, please try again.', 'amalfi' );
		}
	}

	/**
	 * Mask all but the final characters of a license key.
	 *
	 * @param string $key The license key.
	 */
	private function maskKey( string $key ): string {
		$visible = substr( $key, -5 );

		return str_repeat( '*', max( 0, strlen( $key ) - 5 ) ) . $visible;
	}
}
