<?php
/**
 * Title: Hero (Image)
 * Slug: amalfi/hero-image
 * Categories: amalfi_hero
 *
 * @package Amalfi
 */

?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|1000","bottom":"var:preset|spacing|1000"},"margin":{"top":"0"}}},"layout":{"type":"constrained","contentSize":"1440px"}} -->
<div class="wp-block-group alignfull" style="margin-top:0;padding-top:var(--wp--preset--spacing--1000);padding-bottom:var(--wp--preset--spacing--1000)">

	<!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|600","left":"var:preset|spacing|800"}}}} -->
	<div class="wp-block-columns are-vertically-aligned-center">

		<!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%">

			<!-- wp:paragraph {"className":"is-style-overline"} -->
			<p class="is-style-overline">A Travel Magazine · The Amalfi Coast</p>
			<!-- /wp:paragraph -->

			<!-- wp:heading {"level":1,"style":{"spacing":{"margin":{"top":"var:preset|spacing|200"}}},"fontSize":"display-xl"} -->
			<h1 class="wp-block-heading has-display-xl-font-size" style="margin-top:var(--wp--preset--spacing--200)">Slow days on a sunlit coast.</h1>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|300"}}},"fontSize":"body-lg"} -->
			<p class="has-body-lg-font-size" style="margin-top:var(--wp--preset--spacing--300)">Stories, guides, and quiet corners from Positano to Naples — written by travellers who keep coming back to the Costiera Amalfitana.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|500"}}},"layout":{"type":"flex"}} -->
			<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--500)">

				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/amalfi/blog/">Read the stories</a></div>
				<!-- /wp:button -->

				<!-- wp:button {"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/amalfi/destinations/">Explore destinations</a></div>
				<!-- /wp:button -->

			</div>
			<!-- /wp:buttons -->

		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%">

			<!-- wp:image {"sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"8px"}}} -->
			<figure class="wp-block-image size-full has-custom-border"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/positano-coast.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Positano on the Amalfi Coast', 'amalfi' ); ?>" style="border-radius:8px"/></figure>
			<!-- /wp:image -->

		</div>
		<!-- /wp:column -->

	</div>
	<!-- /wp:columns -->

</div>
<!-- /wp:group -->
