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
			new Core\Module(),
			new Admin\Module(),
			new DemoContent\Module(),
			new Licensing\Module(),
			new Updates\Module(),
			new Assets\Module(),
			new Setup\Module(),
		);
		array_map( array( package(), 'addModule' ), $modules );

		package()->boot();

	} catch ( Throwable $throwable ) {
		handleException( $throwable );
	}
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\\initialize', 0 );
