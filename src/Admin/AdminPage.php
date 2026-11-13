<?php
/**
 * Theme setup admin page.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Admin;

/**
 * Registers the "Theme Setup" page under the Appearance menu.
 *
 * The page chrome (header, theme links, screenshot and documentation) is
 * rendered server-side with PHP so it paints instantly. Only the interactive
 * Demo Content and License Settings controls are hydrated client-side: the
 * React app mounts into a single container and its actions go through
 * {@see RestController}.
 */
class AdminPage {

	/**
	 * The DOM id the React app mounts on (the demo/license controls only).
	 */
	private const MOUNT_ID = 'amalfi-demo-license';

	/**
	 * The script/style handle.
	 */
	private const HANDLE = 'amalfi-admin';

	/**
	 * Constructor.
	 *
	 * @param ThemeConfig    $config  The theme configuration.
	 * @param DemoImporter   $demo    The demo importer (for the importable parts).
	 * @param LicenseManager $license The license manager (for the current state).
	 * @param RestController $rest    The REST controller (for the namespace).
	 */
	public function __construct(
		private readonly ThemeConfig $config,
		private readonly DemoImporter $demo,
		private readonly LicenseManager $license,
		private readonly RestController $rest,
	) {
	}

	/**
	 * Register the settings page under Appearance.
	 */
	public function registerPage(): void {
		add_theme_page(
			esc_html__( 'Theme Setup', 'amalfi' ),
			esc_html__( 'Theme Setup', 'amalfi' ),
			$this->config->capability(),
			$this->config->pageSlug(),
			array( $this, 'render' )
		);
	}

	/**
	 * Render the page. The static chrome is server-rendered; the React app
	 * hydrates only the Demo Content / License Settings container.
	 */
	public function render(): void {
		$name    = $this->config->name();
		$version = $this->config->version();
		?>
		<div class="wrap">
			<div class="amalfi-theme-info">

				<h1 class="amalfi-theme-info__title">
					<?php
					/* translators: %1$s: theme name. %2$s: version. */
					printf( esc_html__( 'Welcome to %1$s %2$s', 'amalfi' ), esc_html( $name ), esc_html( $version ) );
					?>
				</h1>

				<?php if ( '' !== $this->config->description() ) : ?>
					<p class="amalfi-theme-info__description">
						<?php echo esc_html( $this->config->description() ); ?>
					</p>
				<?php endif; ?>

				<hr />

				<p class="amalfi-theme-info__links">
					<strong><?php esc_html_e( 'Theme Links:', 'amalfi' ); ?></strong>
					<?php foreach ( $this->links() as $link ) : ?>
						<a href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noreferrer noopener">
							<?php echo esc_html( $link['label'] ); ?>
						</a>
					<?php endforeach; ?>
				</p>

				<hr />

				<section class="amalfi-theme-info__getting-started">
					<h2>
						<?php
						/* translators: %s: theme name. */
						printf( esc_html__( 'Getting started with %s', 'amalfi' ), esc_html( $name ) );
						?>
					</h2>

					<div id="<?php echo esc_attr( self::MOUNT_ID ); ?>" class="amalfi-theme-setup__app">
						<?php esc_html_e( 'Loading…', 'amalfi' ); ?>
					</div>
				</section>

				<hr />

				<section class="amalfi-theme-info__documentation">
					<h2><?php esc_html_e( 'Theme Documentation', 'amalfi' ); ?></h2>
					<p class="amalfi-theme-info__about">
						<?php esc_html_e( 'Need help to set up and configure this theme? We have you covered. Check out the extensive theme documentation on our website.', 'amalfi' ); ?>
					</p>
					<a class="button button-secondary" href="<?php echo esc_url( $this->docsUrl() ); ?>" target="_blank" rel="noreferrer noopener">
						<?php
						/* translators: %s: theme name. */
						printf( esc_html__( "View %s's documentation", 'amalfi' ), esc_html( $name ) );
						?>
					</a>
				</section>

			</div>
		</div>
		<?php
	}

	/**
	 * Enqueue the admin app and inject its bootstrap data.
	 *
	 * @param string $hookSuffix The current admin page hook suffix.
	 */
	public function enqueue( string $hookSuffix ): void {
		if ( 'appearance_page_' . $this->config->pageSlug() !== $hookSuffix ) {
			return;
		}

		$assetsPath = untrailingslashit( $this->config->basePath() ) . '/assets';
		$assetFile  = $assetsPath . '/admin.asset.php';

		if ( ! file_exists( $assetFile ) ) {
			return;
		}

		$asset = include $assetFile;

		wp_enqueue_script(
			self::HANDLE,
			get_theme_file_uri( 'assets/admin.js' ),
			$asset['dependencies'] ?? array(),
			$asset['version'] ?? false,
			true
		);

		wp_set_script_translations( self::HANDLE, 'amalfi' );

		wp_add_inline_script(
			self::HANDLE,
			'window.amalfiThemeSetup = ' . wp_json_encode( $this->bootstrap() ) . ';',
			'before'
		);

		if ( file_exists( $assetsPath . '/admin.css' ) ) {
			wp_enqueue_style(
				self::HANDLE,
				get_theme_file_uri( 'assets/admin.css' ),
				array( 'wp-components' ),
				$asset['version'] ?? false
			);
		} else {
			wp_enqueue_style( 'wp-components' );
		}
	}

	/**
	 * The bootstrap data passed to the React app (the demo/license controls only).
	 *
	 * @return array<string, mixed>
	 */
	private function bootstrap(): array {
		return array(
			'restNamespace' => $this->rest->namespaceRoot(),
			'demoParts'     => $this->demo->parts(),
			'license'       => $this->license->state(),
		);
	}

	/**
	 * The theme links shown in the header.
	 *
	 * @return array<int, array{label: string, url: string}>
	 */
	private function links(): array {
		$slug  = $this->config->slug();
		$store = $this->config->storeUrl();
		$home  = '' !== $this->config->uri() ? $this->config->uri() : $store . '/themes/' . $slug . '/';

		return array(
			array(
				'label' => __( 'Theme Page', 'amalfi' ),
				'url'   => $home,
			),
			array(
				'label' => __( 'Theme Demo', 'amalfi' ),
				'url'   => 'https://preview.themezee.com/?demo=' . $slug,
			),
			array(
				'label' => __( 'Theme Documentation', 'amalfi' ),
				'url'   => $this->docsUrl(),
			),
			array(
				'label' => __( 'Theme Changelog', 'amalfi' ),
				'url'   => $store . '/changelogs/?action=themezee-changelog&type=theme&slug=' . $slug,
			),
		);
	}

	/**
	 * The theme documentation URL.
	 */
	private function docsUrl(): string {
		return $this->config->storeUrl() . '/docs/' . $this->config->slug() . '-documentation/';
	}
}
