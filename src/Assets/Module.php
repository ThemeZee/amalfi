<?php
/**
 * Assets module.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Assets;

use ThemeZee\Packable\Module\ExecutableModule;
use ThemeZee\Packable\Module\ModuleClassNameIdTrait;
use ThemeZee\Packable\Module\ServiceModule;
use ThemeZee\Packable\Package;
use ThemeZee\Packable\Properties\Properties;
use Psr\Container\ContainerInterface;

/**
 * Registers and runs the theme asset services.
 */
class Module implements ServiceModule, ExecutableModule {

	use ModuleClassNameIdTrait;

	/**
	 * Provide the module's service definitions.
	 *
	 * @return array<string, callable> The service factories keyed by class name.
	 */
	public function services(): array {
		return array(
			Editor::class   => static function ( ContainerInterface $container ): Editor {
				/**
				 * Theme properties.
				 *
				 * @var Properties $properties
				 */
				$properties = $container->get( Package::PROPERTIES );

				return new Editor(
					untrailingslashit( $properties->baseUrl() ?? '' ) . '/assets',
				);
			},
			Frontend::class => static function ( ContainerInterface $container ): Frontend {
				/**
				 * Theme properties.
				 *
				 * @var Properties $properties
				 */
				$properties = $container->get( Package::PROPERTIES );

				return new Frontend(
					untrailingslashit( $properties->basePath() ) . '/assets',
					untrailingslashit( $properties->baseUrl() ?? '' ) . '/assets',
				);
			},
		);
	}

	/**
	 * Boot the module by hooking the asset services into WordPress.
	 *
	 * @param ContainerInterface $container The service container.
	 * @return bool True when the module ran successfully.
	 */
	public function run( ContainerInterface $container ): bool {
		add_action(
			'wp_enqueue_scripts',
			$container->get( Frontend::class ),
		);

		add_action(
			'after_setup_theme',
			$container->get( Editor::class ),
		);

		return true;
	}
}
