<?php
/**
 * Title: Blog Posts (Columns)
 * Slug: amalfi/blog-posts-columns
 * Categories: amalfi_blog
 *
 * @package Amalfi
 */

?>

<!-- wp:group {"align":"full","style":{"spacing":{"margin":{"top":"0"}}},"layout":{"type":"constrained","contentSize":"1440px","wideSize":"1440px"}} -->
<div class="wp-block-group alignfull" style="margin-top:0">

	<!-- wp:group {"style":{"border":{"top":{"color":"var:preset|color|primary-200","width":"1px"}},"spacing":{"padding":{"top":"var:preset|spacing|1000","bottom":"var:preset|spacing|1000"},"blockGap":"var:preset|spacing|600"}},"layout":{"type":"constrained","contentSize":"1040px","wideSize":"1040px"}} -->
	<div class="wp-block-group" style="border-top-color:var(--wp--preset--color--primary-200);border-top-width:1px;padding-top:var(--wp--preset--spacing--1000);padding-bottom:var(--wp--preset--spacing--1000)">

		<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|300"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
		<div class="wp-block-group">

			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|150"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group">

				<!-- wp:paragraph {"className":"is-style-eyebrow"} -->
				<p class="is-style-eyebrow">From the journal</p>
				<!-- /wp:paragraph -->

				<!-- wp:heading {"fontSize":"display-sm"} -->
				<h2 class="has-display-sm-font-size">Latest stories</h2>
				<!-- /wp:heading -->

			</div>
			<!-- /wp:group -->

			<!-- wp:buttons -->
			<div class="wp-block-buttons">

				<!-- wp:button {"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/amalfi/blog/">View all stories</a></div>
				<!-- /wp:button -->

			</div>
			<!-- /wp:buttons -->

		</div>
		<!-- /wp:group -->

		<!-- wp:query {"queryId":1,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"displayLayout":{"type":"flex","columns":3}} -->
		<div class="wp-block-query">

			<!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|600"}}} -->

				<!-- wp:group {"layout":{"type":"constrained"}} -->
				<div class="wp-block-group">

					<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2","style":{"border":{"radius":"6px"},"spacing":{"margin":{"bottom":"var:preset|spacing|300"}}}} /-->

					<!-- wp:post-terms {"term":"category"} /-->

					<!-- wp:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"var:preset|spacing|150","bottom":"var:preset|spacing|200"}}},"fontSize":"body-xl"} /-->

					<!-- wp:post-excerpt {"moreText":"Continue reading","excerptLength":18} /-->

				</div>
				<!-- /wp:group -->

			<!-- /wp:post-template -->

		</div>
		<!-- /wp:query -->

	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->
