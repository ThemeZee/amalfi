<?php
/**
 * Title: Query (Default)
 * Slug: amalfi/query-default
 * Block Types: core/query
 * Categories: query
 *
 * @package Amalfi
 */

?>

<!-- wp:query {"queryId":1,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true}} -->
<div class="wp-block-query">

	<!-- wp:post-template -->

		<!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|500","left":"var:preset|spacing|600"},"margin":{"bottom":"var:preset|spacing|800"}}}} -->
		<div class="wp-block-columns are-vertically-aligned-center" style="margin-bottom:var(--wp--preset--spacing--800)">

			<!-- wp:column {"verticalAlignment":"center","width":"40%"} -->
			<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:40%">

				<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3","style":{"border":{"radius":"6px"}}} /-->

			</div>
			<!-- /wp:column -->

			<!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
			<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%">

				<!-- wp:post-terms {"term":"category","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.12rem","fontWeight":"700"},"elements":{"link":{"color":{"text":"var:preset|color|primary-600"}}}},"textColor":"primary-600","fontSize":"body-xs"} /-->

				<!-- wp:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"var:preset|spacing|150","bottom":"var:preset|spacing|200"}}},"fontSize":"display-sm"} /-->

				<!-- wp:post-excerpt {"moreText":"Continue reading","excerptLength":24} /-->

				<!-- wp:post-date {"style":{"spacing":{"margin":{"top":"var:preset|spacing|200"}},"elements":{"link":{"color":{"text":"var:preset|color|primary-600"}}}},"fontSize":"body-sm"} /-->

			</div>
			<!-- /wp:column -->

		</div>
		<!-- /wp:columns -->

	<!-- /wp:post-template -->

	<!-- wp:query-no-results -->

		<!-- wp:paragraph -->
		<p>No posts were found. Try a different search.</p>
		<!-- /wp:paragraph -->

	<!-- /wp:query-no-results -->

	<!-- wp:query-pagination {"style":{"spacing":{"margin":{"top":"var:preset|spacing|600"}}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
		<!-- wp:query-pagination-previous /-->
		<!-- wp:query-pagination-numbers /-->
		<!-- wp:query-pagination-next /-->
	<!-- /wp:query-pagination -->

</div>
<!-- /wp:query -->
