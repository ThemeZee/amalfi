<?php
/**
 * Theme setup admin page.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Admin;

/**
 * Registers the "Theme Setup" page under the Appearance menu and mounts the
 * React application that renders the demo-content and license sections.
 *
 * The page itself is just a root element plus the enqueued script/styles and a
 * bootstrap data blob; all UI lives in the compiled `assets/admin.js` app, and
 * its actions go through {@see RestController}.
 */
class AdminPage {

	/**
	 * The DOM id the React app mounts on.
	 */
	private const ROOT_ID = 'amalfi-theme-setup';

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
	 * Render the page root. The React app replaces its contents.
	 */
	public function render(): void {
		printf(
			'<div class="wrap"><div id="%1$s">%2$s</div></div>',
			esc_attr( self::ROOT_ID ),
			esc_html__( 'Loading…', 'amalfi' )
		);
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
	 * The bootstrap data passed to the React app.
	 *
	 * @return array<string, mixed>
	 */
	private function bootstrap(): array {
		return array(
			'themeName'     => $this->config->name(),
			'version'       => $this->config->version(),
			'restNamespace' => $this->rest->namespaceRoot(),
			'demoParts'     => $this->demo->parts(),
			'license'       => $this->license->state(),
		);
	}
}
