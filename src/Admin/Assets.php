<?php
/**
 * Theme Setup page assets.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Admin;

use ThemeZee\Amalfi\Core\ThemeConfig;
use ThemeZee\Amalfi\DemoContent\Importer;
use ThemeZee\Amalfi\Licensing\LicenseClient;

/**
 * Enqueues the Theme Setup React app and injects its bootstrap data.
 *
 * The bootstrap carries only what the client controls need: the REST namespace,
 * the importable demo parts and the current license state.
 */
class Assets {

	/**
	 * The script/style handle.
	 */
	private const HANDLE = 'amalfi-theme-setup';

	/**
	 * Constructor.
	 *
	 * @param ThemeConfig   $config   The theme configuration.
	 * @param Importer      $importer The demo importer (for the importable parts).
	 * @param LicenseClient $license  The license client (for the current state).
	 */
	public function __construct(
		private readonly ThemeConfig $config,
		private readonly Importer $importer,
		private readonly LicenseClient $license,
	) {
	}

	/**
	 * Enqueue the app and inject its bootstrap data on the Theme Setup page.
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
			'restNamespace' => $this->config->restNamespace(),
			'demoParts'     => $this->importer->parts(),
			'license'       => $this->license->state(),
		);
	}
}
