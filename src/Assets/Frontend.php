<?php
/**
 * Frontend asset handling.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Assets;

/**
 * Enqueues the frontend stylesheet.
 */
readonly class Frontend {

	private const FRONTEND_HANDLE = 'amalfi-frontend';

	/**
	 * Constructor.
	 *
	 * @param string $assetsPath The base filesystem path to the theme assets directory.
	 * @param string $assetsUrl  The base URL to the theme assets directory.
	 */
	public function __construct(
		private string $assetsPath,
		private string $assetsUrl,
	) {
	}

	/**
	 * Enqueue the frontend stylesheet.
	 */
	public function __invoke(): void {
		$assetFile = "{$this->assetsPath}/frontend.asset.php";

		if ( ! file_exists( $assetFile ) ) {
			return;
		}

		$asset = include $assetFile;

		wp_enqueue_style(
			self::MODAL_ASSET_HANDLE,
			"{$this->assetsUrl}/frontend.css",
			$asset['dependencies'] ?? array(),
			$asset['version'] ?? false,
		);
	}
}
