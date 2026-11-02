<?php
/**
 * Theme functions and definitions.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi;

use ThemeZee\Packable\Package;
use ThemeZee\Packable\Properties\ThemeProperties;
use Throwable;

/**
 * Provide the theme instance.
 */
function package(): Package {
	static $package;

	if ( ! $package instanceof Package ) {
		$package = Package::new(
			ThemeProperties::new( basename( __DIR__ ) ),
		);
	}

	return $package;
}

/**
 * Display an error message in the WP admin.
 *
 * @param string $message The error message to display.
 */
function errorNotice( string $message ): void {
	add_action(
		'all_admin_notices',
		static function () use ( $message ) {
			$class = 'notice notice-error';
			printf(
				'<div class="%1$s"><p>%2$s</p></div>',
				esc_attr( $class ),
				wp_kses_post( $message )
			);
		}
	);
}

/**
 * Handle any exception that might occur during theme setup.
 *
 * @param Throwable $throwable The exception to handle.
 */
function handleException( Throwable $throwable ): void {
	errorNotice(
		sprintf(
			'<strong>Error:</strong> %s <br><pre>%s</pre>',
			$throwable->getMessage(),
			$throwable->getTraceAsString()
		)
	);
}

/**
 * Initialize theme
 *
 * @throws Throwable If theme setup fails and the exception cannot be handled.
 */
function initialize(): void {
	try {
		$autoloader = __DIR__ . '/vendor/autoload.php';

		if ( is_readable( $autoloader ) && ! class_exists( Package::class ) ) {
			include_once $autoloader;
		}

		$modules = array(
			new Assets\Module(),
		);
		array_map( array( package(), 'addModule' ), $modules );

		package()->boot();

	} catch ( Throwable $throwable ) {
		handleException( $throwable );
	}
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\\initialize', 0 );

/**
 * Register block pattern categories used by the theme patterns.
 */
function registerBlockPatternCategories(): void {
	$categories = array(
		'amalfi_hero'         => array( 'label' => __( 'Amalfi: Hero', 'amalfi' ) ),
		'amalfi_cta'          => array( 'label' => __( 'Amalfi: Call to Action', 'amalfi' ) ),
		'amalfi_features'     => array( 'label' => __( 'Amalfi: Features', 'amalfi' ) ),
		'amalfi_services'     => array( 'label' => __( 'Amalfi: Services', 'amalfi' ) ),
		'amalfi_testimonials' => array( 'label' => __( 'Amalfi: Testimonials', 'amalfi' ) ),
		'amalfi_team'         => array( 'label' => __( 'Amalfi: Team', 'amalfi' ) ),
		'amalfi_blog'         => array( 'label' => __( 'Amalfi: Blog Posts', 'amalfi' ) ),
	);

	foreach ( $categories as $name => $properties ) {
		if ( ! \WP_Block_Pattern_Categories_Registry::get_instance()->is_registered( $name ) ) {
			register_block_pattern_category( $name, $properties );
		}
	}
}
add_action( 'init', __NAMESPACE__ . '\\registerBlockPatternCategories', 9 );
