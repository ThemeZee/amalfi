<?php
/**
 * Block pattern category registration.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Setup;

use WP_Block_Pattern_Categories_Registry;

/**
 * Registers the block pattern categories used by the theme patterns.
 */
class BlockPatternCategory {

	/**
	 * Register the block pattern categories.
	 */
	public function __invoke(): void {
		$categories = array(
			'amalfi_hero'         => array( 'label' => __( 'Amalfi: Hero', 'amalfi' ) ),
			'amalfi_cta'          => array( 'label' => __( 'Amalfi: Newsletter', 'amalfi' ) ),
			'amalfi_features'     => array( 'label' => __( 'Amalfi: Highlights', 'amalfi' ) ),
			'amalfi_services'     => array( 'label' => __( 'Amalfi: Categories', 'amalfi' ) ),
			'amalfi_testimonials' => array( 'label' => __( 'Amalfi: Testimonials', 'amalfi' ) ),
			'amalfi_team'         => array( 'label' => __( 'Amalfi: Contributors', 'amalfi' ) ),
			'amalfi_blog'         => array( 'label' => __( 'Amalfi: Posts', 'amalfi' ) ),
			'amalfi_page'         => array( 'label' => __( 'Amalfi: Page Sections', 'amalfi' ) ),
		);

		foreach ( $categories as $name => $properties ) {
			if ( ! WP_Block_Pattern_Categories_Registry::get_instance()->is_registered( $name ) ) {
				register_block_pattern_category( $name, $properties );
			}
		}
	}
}
