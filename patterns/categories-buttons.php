<?php
/**
 * Title: Categories (Buttons)
 * Slug: amalfi/categories-buttons
 * Categories: amalfi_categories
 *
 * @package Amalfi
 */

?>

<!-- wp:group {"align":"wide","className":"amalfi-section","style":{"border":{"top":{"color":"var:preset|color|primary-200","width":"1px"}},"spacing":{"padding":{"top":"var:preset|spacing|1000","bottom":"var:preset|spacing|1000"},"blockGap":"var:preset|spacing|500"}},"layout":{"type":"constrained","contentSize":"720px","wideSize":"720px"}} -->
<div class="wp-block-group alignwide amalfi-section" style="border-top-color:var(--wp--preset--color--primary-200);border-top-width:1px;padding-top:var(--wp--preset--spacing--1000);padding-bottom:var(--wp--preset--spacing--1000)">

	<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|150"}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group">

		<!-- wp:paragraph {"align":"center","className":"is-style-overline"} -->
		<p class="has-text-align-center is-style-overline">Explore the topics</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"textAlign":"center","fontSize":"display-sm"} -->
		<h2 class="wp-block-heading has-text-align-center has-display-sm-font-size">What are you in the mood for?</h2>
		<!-- /wp:heading -->

	</div>
	<!-- /wp:group -->

	<!-- wp:buttons {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|150","left":"var:preset|spacing|150"}}},"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"}} -->
	<div class="wp-block-buttons">

		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/category/destinations/' ) ); ?>">Destinations</a></div>
		<!-- /wp:button -->

		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/category/things-to-do/' ) ); ?>">Things to Do</a></div>
		<!-- /wp:button -->

		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/category/where-to-stay/' ) ); ?>">Where to Stay</a></div>
		<!-- /wp:button -->

		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/category/food-drink/' ) ); ?>">Food &amp; Drink</a></div>
		<!-- /wp:button -->

		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/category/travel-tips/' ) ); ?>">Travel Tips</a></div>
		<!-- /wp:button -->

		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/category/photography/' ) ); ?>">Photography</a></div>
		<!-- /wp:button -->

	</div>
	<!-- /wp:buttons -->

</div>
<!-- /wp:group -->
