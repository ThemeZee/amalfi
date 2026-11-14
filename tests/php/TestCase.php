<?php
/**
 * Base test case for the Amalfi unit tests.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Tests;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use ThemeZee\Amalfi\Core\ThemeConfig;

/**
 * Sets up Brain Monkey (WordPress function mocking) and stubs the common
 * i18n/escaping helpers as pass-throughs so individual tests only mock the
 * functions whose behaviour they actually assert.
 */
abstract class TestCase extends PHPUnitTestCase {

	use MockeryPHPUnitIntegration;

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();

		// Pass-through stubs: `null` makes the stub return its first argument.
		Functions\stubs(
			array(
				'__'                  => null,
				'esc_html'            => null,
				'esc_html__'          => null,
				'esc_attr'            => null,
				'esc_attr__'          => null,
				'esc_url'             => null,
				'esc_url_raw'         => null,
				'esc_js'              => null,
				'wp_kses_post'        => null,
				'sanitize_text_field' => null,
			)
		);
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Build a real ThemeConfig (it is a final value object, so it is used
	 * directly rather than mocked). Any field can be overridden by key.
	 *
	 * @param array<string, mixed> $overrides Field overrides.
	 */
	protected function themeConfig( array $overrides = array() ): ThemeConfig {
		$args = array_merge(
			array(
				'name'       => 'Amalfi',
				'slug'       => 'amalfi',
				'version'    => '1.0.0',
				'author'     => 'ThemeZee',
				'textDomain' => 'amalfi',
				'basePath'   => '/var/www/themes/amalfi',
				'itemId'     => 123,
				'storeUrl'   => 'https://store.test',
			),
			$overrides
		);

		return new ThemeConfig(
			$args['name'],
			$args['slug'],
			$args['version'],
			$args['author'],
			$args['textDomain'],
			$args['basePath'],
			'',
			'',
			$args['itemId'],
			$args['storeUrl'],
		);
	}
}
