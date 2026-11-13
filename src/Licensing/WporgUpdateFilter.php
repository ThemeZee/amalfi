<?php
/**
 * WordPress.org update-check filter.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Licensing;

/**
 * Removes this theme from the wp.org theme update check, so updates come from
 * the ThemeZee store instead of the WordPress.org repository.
 */
class WporgUpdateFilter {

	/**
	 * Strip the active parent/child theme from the wp.org update-check request.
	 *
	 * @param array<string, mixed> $args The HTTP request arguments.
	 * @param string               $url  The request URL.
	 * @return array<string, mixed>
	 */
	public function __invoke( array $args, string $url ): array {
		if ( ! str_starts_with( $url, 'https://api.wordpress.org/themes/update-check/1.1/' ) ) {
			return $args;
		}

		if ( empty( $args['body']['themes'] ) ) {
			return $args;
		}

		$themes = json_decode( $args['body']['themes'] );

		if ( ! isset( $themes->themes ) ) {
			return $args;
		}

		$parent = get_option( 'template' );
		$child  = get_option( 'stylesheet' );
		unset( $themes->themes->$parent, $themes->themes->$child );

		$args['body']['themes'] = wp_json_encode( $themes );

		return $args;
	}
}
