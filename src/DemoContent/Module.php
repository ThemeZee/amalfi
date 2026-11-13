<?php
/**
 * Demo Content module.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\DemoContent;

use ThemeZee\Amalfi\Core\ThemeConfig;
use ThemeZee\Packable\Module\ExecutableModule;
use ThemeZee\Packable\Module\ModuleClassNameIdTrait;
use ThemeZee\Packable\Module\ServiceModule;
use Psr\Container\ContainerInterface;

/**
 * Provides the demo-content importer and its REST route.
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
			Content::class        => static fn ( ContainerInterface $c ): Content => new Content(
				$c->get( ThemeConfig::class )
			),
			Importer::class       => static fn ( ContainerInterface $c ): Importer => new Importer(
				$c->get( Content::class )
			),
			RestController::class => static fn ( ContainerInterface $c ): RestController => new RestController(
				$c->get( ThemeConfig::class ),
				$c->get( Importer::class )
			),
		);
	}

	/**
	 * Boot the module by registering the REST route.
	 *
	 * @param ContainerInterface $container The service container.
	 * @return bool True when the module ran successfully.
	 */
	public function run( ContainerInterface $container ): bool {
		add_action(
			'rest_api_init',
			array( $container->get( RestController::class ), 'registerRoutes' )
		);

		return true;
	}
}
