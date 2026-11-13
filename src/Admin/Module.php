<?php
/**
 * Admin module.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Admin;

use ThemeZee\Amalfi\Core\ThemeConfig;
use ThemeZee\Amalfi\DemoContent\Importer;
use ThemeZee\Amalfi\Licensing\LicenseClient;
use ThemeZee\Packable\Module\ExecutableModule;
use ThemeZee\Packable\Module\ModuleClassNameIdTrait;
use ThemeZee\Packable\Module\ServiceModule;
use Psr\Container\ContainerInterface;

/**
 * Registers the Theme Setup page under Appearance and enqueues its React app.
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
			Page::class   => static fn ( ContainerInterface $c ): Page => new Page(
				$c->get( ThemeConfig::class )
			),
			Assets::class => static fn ( ContainerInterface $c ): Assets => new Assets(
				$c->get( ThemeConfig::class ),
				$c->get( Importer::class ),
				$c->get( LicenseClient::class )
			),
		);
	}

	/**
	 * Boot the module by hooking the page and its assets into WordPress.
	 *
	 * @param ContainerInterface $container The service container.
	 * @return bool True when the module ran successfully.
	 */
	public function run( ContainerInterface $container ): bool {
		add_action( 'admin_menu', array( $container->get( Page::class ), 'registerPage' ), 9 );
		add_action( 'admin_enqueue_scripts', array( $container->get( Assets::class ), 'enqueue' ) );

		return true;
	}
}
