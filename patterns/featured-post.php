<?php
/**
 * Title: Featured Story
 * Slug: amalfi/featured-post
 * Categories: amalfi_blog
 *
 * @package Amalfi
 */

?>

<!-- wp:group {"align":"full","style":{"spacing":{"margin":{"top":"0"}}},"layout":{"type":"constrained","contentSize":"1440px","wideSize":"1440px"}} -->
<div class="wp-block-group alignfull" style="margin-top:0">

	<!-- wp:group {"style":{"border":{"top":{"color":"var:preset|color|primary-200","width":"1px"}},"spacing":{"padding":{"top":"var:preset|spacing|1000","bottom":"var:preset|spacing|1000"},"blockGap":"var:preset|spacing|600"}},"layout":{"type":"constrained","contentSize":"1040px","wideSize":"1040px"}} -->
	<div class="wp-block-group" style="border-top-color:var(--wp--preset--color--primary-200);border-top-width:1px;padding-top:var(--wp--preset--spacing--1000);padding-bottom:var(--wp--preset--spacing--1000)">

		<!-- wp:paragraph {"className":"is-style-eyebrow"} -->
		<p class="is-style-eyebrow">Featured story</p>
		<!-- /wp:paragraph -->

		<!-- wp:query {"queryId":2,"query":{"perPage":1,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"align":"wide"} -->
		<div class="wp-block-query alignwide">

			<!-- wp:post-template -->

				<!-- wp:columns {"verticalAlignment":"top","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|500","left":"var:preset|spacing|600"},"margin":{"bottom":"0"}}}} -->
				<div class="wp-block-columns are-vertically-aligned-top" style="margin-bottom:0">

					<!-- wp:column {"verticalAlignment":"top"} -->
					<div class="wp-block-column is-vertically-aligned-top">

						<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3","style":{"border":{"radius":"8px"}}} /-->

					</div>
					<!-- /wp:column -->

					<!-- wp:column {"verticalAlignment":"top"} -->
					<div class="wp-block-column is-vertically-aligned-top">

						<!-- wp:post-terms {"term":"category"} /-->

						<!-- wp:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"var:preset|spacing|200","bottom":"var:preset|spacing|300"}}},"fontSize":"display-md"} /-->

						<!-- wp:post-excerpt {"moreText":"Read the story","excerptLength":28} /-->

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

</div>
<!-- /wp:group -->
