<?php
/**
 * Core module.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Core;

use ThemeZee\Packable\Module\ModuleClassNameIdTrait;
use ThemeZee\Packable\Module\ServiceModule;
use ThemeZee\Packable\Package;
use ThemeZee\Packable\Properties\Properties;
use Psr\Container\ContainerInterface;

/**
 * Provides the shared services the feature modules build on: the theme
 * configuration and the settings repository.
 *
 * This module only registers services into the container; the feature modules
 * (Admin, DemoContent, Licensing, Updates) resolve them from there.
 */
class Module implements ServiceModule {

	use ModuleClassNameIdTrait;

	/**
	 * Provide the module's service definitions.
	 *
	 * @return array<string, callable> The service factories keyed by class name.
	 */
	public function services(): array {
		return array(
			ThemeConfig::class        => static function ( ContainerInterface $container ): ThemeConfig {
				/**
				 * Theme properties.
				 *
				 * @var Properties $properties
				 */
				$properties = $container->get( Package::PROPERTIES );

				return new ThemeConfig(
					$properties->name(),
					$properties->baseName(),
					$properties->version(),
					$properties->author(),
					$properties->textDomain(),
					untrailingslashit( $properties->basePath() ),
					$properties->description(),
					$properties->uri(),
				);
			},
			SettingsRepository::class => static fn ( ContainerInterface $c ): SettingsRepository => new SettingsRepository(
				$c->get( ThemeConfig::class )
			),
		);
	}
}
