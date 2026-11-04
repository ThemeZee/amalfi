<?php
/**
 * Setup module.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Setup;

use ThemeZee\Packable\Module\ExecutableModule;
use ThemeZee\Packable\Module\ModuleClassNameIdTrait;
use ThemeZee\Packable\Module\ServiceModule;
use Psr\Container\ContainerInterface;

/**
 * Registers and runs the theme setup services.
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
			BlockPatternCategory::class => static fn () => new BlockPatternCategory(),
			DefaultBlockPatterns::class => static fn () => new DefaultBlockPatterns(),
		);
	}

	/**
	 * Boot the module by hooking the setup services into WordPress.
	 *
	 * @param ContainerInterface $container The service container.
	 * @return bool True when the module ran successfully.
	 */
	public function run( ContainerInterface $container ): bool {
		add_action(
			'init',
			$container->get( BlockPatternCategory::class ),
			9,
		);

		$defaultBlockPatterns = $container->get( DefaultBlockPatterns::class );

		add_action(
			'after_setup_theme',
			array( $defaultBlockPatterns, 'removeThemeSupport' ),
		);

		add_filter( 'should_load_remote_block_patterns', '__return_false' );

		add_action(
			'init',
			array( $defaultBlockPatterns, 'unregisterDefaults' ),
			99,
		);

		return true;
	}
}
