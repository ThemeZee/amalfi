<?php
/**
 * Title: Hero (Centered)
 * Slug: amalfi/hero-centered
 * Categories: amalfi_hero
 *
 * @package Amalfi
 */

?>

<!-- wp:group {"align":"full","className":"amalfi-section","style":{"spacing":{"padding":{"top":"var:preset|spacing|1000","right":"var:preset|spacing|400","bottom":"var:preset|spacing|1000","left":"var:preset|spacing|400"}}},"layout":{"type":"constrained","contentSize":"720px"}} -->
<div class="wp-block-group alignfull amalfi-section" style="padding-top:var(--wp--preset--spacing--1000);padding-right:var(--wp--preset--spacing--400);padding-bottom:var(--wp--preset--spacing--1000);padding-left:var(--wp--preset--spacing--400)">

	<!-- wp:paragraph {"align":"center","className":"is-style-overline"} -->
	<p class="has-text-align-center is-style-overline">Stories from the Costiera</p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"textAlign":"center","level":1,"style":{"spacing":{"margin":{"top":"var:preset|spacing|300"}}},"fontSize":"display-xl"} -->
	<h1 class="wp-block-heading has-text-align-center has-display-xl-font-size" style="margin-top:var(--wp--preset--spacing--300)">Find the quiet side of the coast</h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|300"}}},"fontSize":"body-lg"} -->
	<p class="has-text-align-center has-body-lg-font-size" style="margin-top:var(--wp--preset--spacing--300)">Guides, essays, and small discoveries for travellers who would rather stay a week than tick off a day. Pour a coffee and settle in.</p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|500"}}},"layout":{"type":"flex","justifyContent":"center"}} -->
	<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--500)">

		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">Read the stories</a></div>
		<!-- /wp:button -->

		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About the journal</a></div>
		<!-- /wp:button -->

	</div>
	<!-- /wp:buttons -->

</div>
<!-- /wp:group -->
