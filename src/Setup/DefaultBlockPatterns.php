<?php
/**
 * Removal of the default WordPress block patterns and categories.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Setup;

use WP_Block_Pattern_Categories_Registry;
use WP_Block_Patterns_Registry;

/**
 * Removes the block patterns and pattern categories bundled with WordPress core
 * so the editor only offers the patterns shipped by the theme.
 */
class DefaultBlockPatterns {

	/**
	 * Disable the block patterns bundled with WordPress core.
	 *
	 * Runs on `after_setup_theme` so the removed support is in place before core
	 * checks for it when registering its patterns on `init`.
	 */
	public function removeThemeSupport(): void {
		remove_theme_support( 'core-block-patterns' );
	}

	/**
	 * Unregister the remaining core patterns and the categories left unused.
	 *
	 * Dropping the `core-block-patterns` theme support stops most core patterns
	 * from being registered, but a few (such as the navigation overlays) are
	 * registered independently, and the core pattern categories are always
	 * registered regardless of that support. This removes any pattern still
	 * flagged as a core pattern and then every category no remaining theme
	 * pattern references, so the inserter only lists the theme's own patterns.
	 *
	 * Runs late on `init` so core and theme patterns and categories are registered.
	 */
	public function unregisterDefaults(): void {
		$patterns = WP_Block_Patterns_Registry::get_instance();

		foreach ( $patterns->get_all_registered() as $pattern ) {
			if ( ( $pattern['source'] ?? '' ) === 'core' ) {
				unregister_block_pattern( $pattern['name'] );
			}
		}

		$usedCategories = array();

		foreach ( $patterns->get_all_registered() as $pattern ) {
			foreach ( $pattern['categories'] ?? array() as $category ) {
				$usedCategories[ $category ] = true;
			}
		}

		$categories = WP_Block_Pattern_Categories_Registry::get_instance();

		foreach ( $categories->get_all_registered() as $category ) {
			if ( ! isset( $usedCategories[ $category['name'] ] ) ) {
				unregister_block_pattern_category( $category['name'] );
			}
		}
	}
}
