<?php
/**
 * Editor asset handling.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Assets;

/**
 * Registers the block editor stylesheet.
 */
class Editor {

	/**
	 * Constructor.
	 *
	 * @param string $assetsUrl The base URL to the theme assets directory.
	 */
	public function __construct(
		private readonly string $assetsUrl,
	) {
	}

	/**
	 * Add the editor stylesheet.
	 */
	public function __invoke(): void {
		add_editor_style( "{$this->assetsUrl}/editor.css" );
	}
}
