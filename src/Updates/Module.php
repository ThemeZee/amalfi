<?php
/**
 * Updates module.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Updates;

use ThemeZee\Amalfi\Core\SettingsRepository;
use ThemeZee\Amalfi\Core\ThemeConfig;
use ThemeZee\Packable\Module\ExecutableModule;
use ThemeZee\Packable\Module\ModuleClassNameIdTrait;
use ThemeZee\Packable\Module\ServiceModule;
use Psr\Container\ContainerInterface;

/**
 * Provides the theme updater that checks the ThemeZee store for updates.
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
			ThemeUpdater::class => static fn ( ContainerInterface $c ): ThemeUpdater => new ThemeUpdater(
				$c->get( ThemeConfig::class ),
				$c->get( SettingsRepository::class )
			),
		);
	}

	/**
	 * Boot the module by registering the updater.
	 *
	 * The updater runs on `init` so it can participate in the `wp_version_check`
	 * cron job for auto-updates, while staying limited to privileged users
	 * during normal requests.
	 *
	 * @param ContainerInterface $container The service container.
	 * @return bool True when the module ran successfully.
	 */
	public function run( ContainerInterface $container ): bool {
		add_action(
			'init',
			static function () use ( $container ): void {
				$doingCron = defined( 'DOING_CRON' ) && DOING_CRON;

				if ( ! current_user_can( 'manage_options' ) && ! $doingCron ) {
					return;
				}

				$container->get( ThemeUpdater::class )->register();
			}
		);

		return true;
	}
}
