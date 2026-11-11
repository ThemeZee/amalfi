<?php
/**
 * Title: Blog Posts (List)
 * Slug: amalfi/blog-posts-list
 * Categories: amalfi_posts
 *
 * @package Amalfi
 */

?>

<!-- wp:group {"align":"wide","style":{"border":{"top":{"color":"var:preset|color|primary-200","width":"1px"}},"spacing":{"margin":{"top":"0"},"padding":{"top":"var:preset|spacing|1000","bottom":"var:preset|spacing|1000"},"blockGap":"var:preset|spacing|600"}},"layout":{"type":"constrained","contentSize":"800px","wideSize":"800px"}} -->
<div class="wp-block-group alignwide" style="border-top-color:var(--wp--preset--color--primary-200);border-top-width:1px;padding-top:var(--wp--preset--spacing--1000);padding-bottom:var(--wp--preset--spacing--1000);margin-top:0">

	<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|150"}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group">

		<!-- wp:paragraph {"className":"is-style-overline"} -->
		<p class="is-style-overline">From the journal</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"fontSize":"display-sm"} -->
		<h2 class="wp-block-heading has-display-sm-font-size">Latest stories</h2>
		<!-- /wp:heading -->

	</div>
	<!-- /wp:group -->

	<!-- wp:query {"queryId":3,"query":{"perPage":4,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false}} -->
	<div class="wp-block-query">

		<!-- wp:post-template {"style":{"spacing":{"blockGap":"0"}}} -->

			<!-- wp:columns {"verticalAlignment":"top","style":{"border":{"top":{"color":"var:preset|color|primary-200","width":"1px"}},"spacing":{"padding":{"top":"var:preset|spacing|500","bottom":"var:preset|spacing|500"},"blockGap":{"top":"var:preset|spacing|200","left":"var:preset|spacing|600"}}}} -->
			<div class="wp-block-columns are-vertically-aligned-top" style="border-top-color:var(--wp--preset--color--primary-200);border-top-width:1px;padding-top:var(--wp--preset--spacing--500);padding-bottom:var(--wp--preset--spacing--500)">

				<!-- wp:column {"verticalAlignment":"top","width":"30%"} -->
				<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:30%">

					<!-- wp:post-terms {"term":"category"} /-->

					<!-- wp:post-date {"style":{"spacing":{"margin":{"top":"var:preset|spacing|100"}},"elements":{"link":{"color":{"text":"var:preset|color|primary-600"}}}},"textColor":"primary-600","fontSize":"body-sm"} /-->

				</div>
				<!-- /wp:column -->

				<!-- wp:column {"verticalAlignment":"top"} -->
				<div class="wp-block-column is-vertically-aligned-top">

					<!-- wp:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"0","bottom":"var:preset|spacing|200"}}},"fontSize":"body-xl"} /-->

					<!-- wp:post-excerpt {"moreText":"Read the story","excerptLength":22} /-->

				</div>
				<!-- /wp:column -->

			</div>
			<!-- /wp:columns -->

		<!-- /wp:post-template -->

	</div>
	<!-- /wp:query -->

	<!-- wp:buttons -->
	<div class="wp-block-buttons">

		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/amalfi/blog/">View all stories</a></div>
		<!-- /wp:button -->

	</div>
	<!-- /wp:buttons -->

</div>
<!-- /wp:group -->
