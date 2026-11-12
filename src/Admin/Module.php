<?php
/**
 * Admin module.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Admin;

use ThemeZee\Packable\Module\ExecutableModule;
use ThemeZee\Packable\Module\ModuleClassNameIdTrait;
use ThemeZee\Packable\Module\ServiceModule;
use ThemeZee\Packable\Package;
use ThemeZee\Packable\Properties\Properties;
use Psr\Container\ContainerInterface;

/**
 * Registers and runs the theme's admin services: the "Theme Setup" page under
 * Appearance (demo content import and license activation) and the theme updater.
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
				);
			},
			SettingsRepository::class => static fn ( ContainerInterface $c ): SettingsRepository => new SettingsRepository(
				$c->get( ThemeConfig::class )
			),
			DemoContent::class        => static fn ( ContainerInterface $c ): DemoContent => new DemoContent(
				$c->get( ThemeConfig::class )
			),
			DemoImporter::class       => static fn ( ContainerInterface $c ): DemoImporter => new DemoImporter(
				$c->get( ThemeConfig::class ),
				$c->get( SettingsRepository::class ),
				$c->get( DemoContent::class )
			),
			LicenseManager::class     => static fn ( ContainerInterface $c ): LicenseManager => new LicenseManager(
				$c->get( ThemeConfig::class ),
				$c->get( SettingsRepository::class )
			),
			RestController::class     => static fn ( ContainerInterface $c ): RestController => new RestController(
				$c->get( ThemeConfig::class ),
				$c->get( DemoImporter::class ),
				$c->get( LicenseManager::class )
			),
			AdminPage::class          => static fn ( ContainerInterface $c ): AdminPage => new AdminPage(
				$c->get( ThemeConfig::class ),
				$c->get( DemoImporter::class ),
				$c->get( LicenseManager::class ),
				$c->get( RestController::class )
			),
			ThemeUpdater::class       => static fn ( ContainerInterface $c ): ThemeUpdater => new ThemeUpdater(
				$c->get( ThemeConfig::class ),
				$c->get( SettingsRepository::class )
			),
		);
	}

	/**
	 * Boot the module by hooking the admin services into WordPress.
	 *
	 * @param ContainerInterface $container The service container.
	 * @return bool True when the module ran successfully.
	 */
	public function run( ContainerInterface $container ): bool {
		$this->runAdminPage( $container );
		$this->runRest( $container );
		$this->runLicenseHooks( $container );
		$this->runUpdater( $container );

		return true;
	}

	/**
	 * Wire the admin page: register the menu entry and enqueue the React app.
	 *
	 * @param ContainerInterface $container The service container.
	 */
	private function runAdminPage( ContainerInterface $container ): void {
		$page = $container->get( AdminPage::class );

		add_action( 'admin_menu', array( $page, 'registerPage' ), 9 );
		add_action( 'admin_enqueue_scripts', array( $page, 'enqueue' ) );
	}

	/**
	 * Register the REST routes that power the admin page.
	 *
	 * @param ContainerInterface $container The service container.
	 */
	private function runRest( ContainerInterface $container ): void {
		add_action(
			'rest_api_init',
			array( $container->get( RestController::class ), 'registerRoutes' )
		);
	}

	/**
	 * Wire the license reminder notice and the wp.org update filter.
	 *
	 * @param ContainerInterface $container The service container.
	 */
	private function runLicenseHooks( ContainerInterface $container ): void {
		$license = $container->get( LicenseManager::class );

		add_action( 'admin_notices', array( $license, 'renderReminder' ) );
		add_filter( 'http_request_args', array( $license, 'disableWporgRequest' ), 5, 2 );
	}

	/**
	 * Wire the theme updater.
	 *
	 * The updater runs on `init` so it can participate in the `wp_version_check`
	 * cron job for auto-updates, while staying limited to privileged users
	 * during normal requests.
	 *
	 * @param ContainerInterface $container The service container.
	 */
	private function runUpdater( ContainerInterface $container ): void {
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
	}
}
