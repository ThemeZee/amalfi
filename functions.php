<?php

declare(strict_types=1);

namespace ThemeZee\Amalfi;

use ThemeZee\Packable\Package;
use ThemeZee\Packable\Properties\ThemeProperties;
use Throwable;

/**
 * Provide the theme instance.
 */
function package(): Package
{
    static $package;

    if (!$package instanceof Package) {
        $package = Package::new(
            ThemeProperties::new(basename(__DIR__)),
        );
    }

    return $package;
}

/**
 * Display an error message in the WP admin.
 */
function errorNotice(string $message): void
{
    add_action(
        'all_admin_notices',
        static function () use ($message) {
            $class = 'notice notice-error';
            printf(
                '<div class="%1$s"><p>%2$s</p></div>',
                esc_attr($class),
                wp_kses_post($message)
            );
        }
    );
}

/**
 * Handle any exception that might occur during theme setup.
 */
function handleException(Throwable $throwable): void
{
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
 * @throws Throwable
 */
function initialize(): void
{
    try {
        $autoloader = __DIR__ . '/vendor/autoload.php';

        if (is_readable($autoloader) && !class_exists(Package::class)) {
            include_once $autoloader;
        }

		$modules = [

		];
		array_map([package(), 'addModule'], $modules);

		package()->boot();

    } catch (Throwable $throwable) {
        handleException($throwable);
    }
}
add_action('after_setup_theme', __NAMESPACE__ . '\\initialize');
