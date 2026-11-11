<?php
/**
 * Block style registration.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Setup;

/**
 * Registers the custom block styles provided by the theme.
 *
 * The visual styling for each block style lives in the theme stylesheet
 * (see `resources/scss/global/_block-styles.scss`) and is intentionally kept
 * free of `!important`, so any block setting a user configures in the editor
 * still overrides the block style defaults.
 */
class BlockStyle {

	/**
	 * Register the theme's block styles.
	 */
	public function __invoke(): void {
		register_block_style(
			'core/navigation',
			array(
				'name'  => 'main-navigation',
				'label' => __( 'Main Navigation', 'amalfi' ),
			)
		);

		register_block_style(
			'core/paragraph',
			array(
				'name'  => 'overline',
				'label' => __( 'Overline', 'amalfi' ),
			)
		);

		register_block_style(
			'core/heading',
			array(
				'name'  => 'overline',
				'label' => __( 'Overline', 'amalfi' ),
			)
		);

		register_block_style(
			'core/social-links',
			array(
				'name'  => 'primary',
				'label' => __( 'Primary', 'amalfi' ),
			)
		);

		register_block_style(
			'core/social-links',
			array(
				'name'  => 'outline',
				'label' => __( 'Outline', 'amalfi' ),
			)
		);
	}
}
