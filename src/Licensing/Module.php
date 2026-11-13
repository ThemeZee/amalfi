<?php
/**
 * Licensing module.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Licensing;

use ThemeZee\Amalfi\Core\SettingsRepository;
use ThemeZee\Amalfi\Core\ThemeConfig;
use ThemeZee\Packable\Module\ExecutableModule;
use ThemeZee\Packable\Module\ModuleClassNameIdTrait;
use ThemeZee\Packable\Module\ServiceModule;
use Psr\Container\ContainerInterface;

/**
 * Provides the license client, its REST route, the activation reminder notice
 * and the wp.org update-check filter.
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
			LicenseClient::class     => static fn ( ContainerInterface $c ): LicenseClient => new LicenseClient(
				$c->get( ThemeConfig::class ),
				$c->get( SettingsRepository::class )
			),
			Reminder::class          => static fn ( ContainerInterface $c ): Reminder => new Reminder(
				$c->get( ThemeConfig::class ),
				$c->get( SettingsRepository::class )
			),
			WporgUpdateFilter::class => static fn (): WporgUpdateFilter => new WporgUpdateFilter(),
			RestController::class    => static fn ( ContainerInterface $c ): RestController => new RestController(
				$c->get( ThemeConfig::class ),
				$c->get( LicenseClient::class )
			),
		);
	}

	/**
	 * Boot the module by hooking the license services into WordPress.
	 *
	 * @param ContainerInterface $container The service container.
	 * @return bool True when the module ran successfully.
	 */
	public function run( ContainerInterface $container ): bool {
		add_action(
			'rest_api_init',
			array( $container->get( RestController::class ), 'registerRoutes' )
		);

		add_action( 'admin_notices', array( $container->get( Reminder::class ), 'render' ) );

		add_filter( 'http_request_args', $container->get( WporgUpdateFilter::class ), 5, 2 );

		return true;
	}
}
