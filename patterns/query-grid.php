<?php
/**
 * Title: Query (Grid)
 * Slug: amalfi/query-grid
 * Block Types: core/query
 * Categories: amalfi_query
 *
 * @package Amalfi
 */

?>

<!-- wp:query {"queryId":1,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"displayLayout":{"type":"flex","columns":3}} -->
<div class="wp-block-query">

	<!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|600"}}} -->

		<!-- wp:group {"layout":{"type":"constrained"}} -->
		<div class="wp-block-group">

			<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|300"}}}} /-->

			<!-- wp:post-terms {"term":"category"} /-->

			<!-- wp:post-title {"isLink":true,"style":{"spacing":{"margin":{"top":"var:preset|spacing|150","bottom":"var:preset|spacing|200"}}},"fontSize":"body-xl"} /-->

			<!-- wp:post-excerpt {"moreText":"Continue reading","excerptLength":18} /-->

		</div>
		<!-- /wp:group -->

	<!-- /wp:post-template -->

	<!-- wp:query-no-results -->

		<!-- wp:paragraph -->
		<p>No posts were found. Try a different search.</p>
		<!-- /wp:paragraph -->

	<!-- /wp:query-no-results -->

	<!-- wp:query-pagination {"style":{"spacing":{"margin":{"top":"var:preset|spacing|800"}}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
		<!-- wp:query-pagination-previous /-->
		<!-- wp:query-pagination-numbers /-->
		<!-- wp:query-pagination-next /-->
	<!-- /wp:query-pagination -->

</div>
<!-- /wp:query -->
