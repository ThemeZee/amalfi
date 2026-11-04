<?php
/**
 * Title: Featured Story
 * Slug: amalfi/featured-post
 * Categories: amalfi_blog
 *
 * @package Amalfi
 */

?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|1000","right":"var:preset|spacing|400","bottom":"var:preset|spacing|1000","left":"var:preset|spacing|400"},"margin":{"top":"0"},"blockGap":"var:preset|spacing|600"}},"layout":{"type":"constrained","contentSize":"1040px"}} -->
<div class="wp-block-group alignfull" style="margin-top:0;padding-top:var(--wp--preset--spacing--1000);padding-right:var(--wp--preset--spacing--400);padding-bottom:var(--wp--preset--spacing--1000);padding-left:var(--wp--preset--spacing--400)">

	<!-- wp:paragraph {"className":"is-style-eyebrow"} -->
	<p class="is-style-eyebrow">Featured story</p>
	<!-- /wp:paragraph -->

	<!-- wp:query {"queryId":2,"query":{"perPage":1,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"align":"wide"} -->
	<div class="wp-block-query alignwide">

		<!-- wp:post-template -->

			<!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|500","left":"var:preset|spacing|800"}}}} -->
			<div class="wp-block-columns are-vertically-aligned-center">

				<!-- wp:column {"verticalAlignment":"center","width":"58%"} -->
				<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:58%">

					<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3","style":{"border":{"radius":"8px"}}} /-->

				</div>
				<!-- /wp:column -->

				<!-- wp:column {"verticalAlignment":"center","width":"42%"} -->
				<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:42%">

					<!-- wp:post-terms {"term":"category","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.12rem","fontWeight":"700"},"elements":{"link":{"color":{"text":"var:preset|color|primary-600"}}}},"textColor":"primary-600","fontSize":"body-xs"} /-->

					<!-- wp:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"var:preset|spacing|200","bottom":"var:preset|spacing|300"}}},"fontSize":"display-md"} /-->

					<!-- wp:post-excerpt {"moreText":"Read the story","excerptLength":32} /-->

					<!-- wp:post-date {"style":{"spacing":{"margin":{"top":"var:preset|spacing|300"}},"elements":{"link":{"color":{"text":"var:preset|color|primary-600"}}}},"fontSize":"body-sm"} /-->

				</div>
				<!-- /wp:column -->

			</div>
			<!-- /wp:columns -->

		<!-- /wp:post-template -->

	</div>
	<!-- /wp:query -->

</div>
<!-- /wp:group -->
