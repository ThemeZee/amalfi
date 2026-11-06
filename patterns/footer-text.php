<?php
/**
 * Title: Footer Text
 * Slug: amalfi/footer-text
 * Inserter: no
 *
 * @package Amalfi
 */

?>

<!-- wp:paragraph {"fontSize":"body-sm"} -->
<p class="has-body-sm-font-size">&copy; <?php echo wp_kses_post( date_i18n( 'Y' ) . ' ' . get_bloginfo( 'name' ) ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"body-sm"} -->
<p class="has-body-sm-font-size"><a href="#"><?php esc_html_e( 'Privacy Policy', 'amalfi' ); ?></a> &middot; <a href="#"><?php esc_html_e( 'Imprint', 'amalfi' ); ?></a></p>
<!-- /wp:paragraph -->
