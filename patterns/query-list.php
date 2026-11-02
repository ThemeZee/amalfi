<?php
/**
 * Title: Query (List)
 * Slug: amalfi/query-list
 * Block Types: core/query
 * Categories: query
 *
 * @package Amalfi
 */

?>

<!-- wp:query {"queryId":1,"query":{"perPage":8,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true}} -->
<div class="wp-block-query">

	<!-- wp:post-template -->

		<!-- wp:group {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|600"},"padding":{"bottom":"var:preset|spacing|600"}},"border":{"bottom":{"color":"var:preset|color|primary-200","width":"1px"},"top":{},"right":{},"left":{}}},"layout":{"type":"constrained"}} -->
		<div class="wp-block-group" style="border-bottom-color:var(--wp--preset--color--primary-200);border-bottom-width:1px;margin-bottom:var(--wp--preset--spacing--600);padding-bottom:var(--wp--preset--spacing--600)">

			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|300"}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
			<div class="wp-block-group">

				<!-- wp:post-terms {"term":"category","style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.12rem","fontWeight":"700"},"elements":{"link":{"color":{"text":"var:preset|color|primary-600"}}}},"textColor":"primary-600","fontSize":"body-xs"} /-->
				<!-- wp:post-date {"style":{"elements":{"link":{"color":{"text":"var:preset|color|primary-600"}}}},"fontSize":"body-xs"} /-->

			</div>
			<!-- /wp:group -->

			<!-- wp:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"var:preset|spacing|150","bottom":"var:preset|spacing|200"}}},"fontSize":"display"} /-->

			<!-- wp:post-excerpt {"moreText":"Continue reading","excerptLength":28} /-->

		</div>
		<!-- /wp:group -->

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
