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
	 * Add the editor stylesheet.
	 */
	public function __invoke(): void {
		add_editor_style( 'assets/editor.css' );
	}
}
