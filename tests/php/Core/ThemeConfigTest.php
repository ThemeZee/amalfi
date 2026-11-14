<?php
/**
 * Tests for ThemeConfig.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Tests\Core;

use Brain\Monkey\Functions;
use ThemeZee\Amalfi\Core\ThemeConfig;
use ThemeZee\Amalfi\Tests\TestCase;

final class ThemeConfigTest extends TestCase {

	private function config( array $overrides = array() ): ThemeConfig {
		$defaults = array(
			'name'       => 'Amalfi',
			'slug'       => 'amalfi',
			'version'    => '1.0.0',
			'author'     => 'ThemeZee',
			'textDomain' => 'amalfi',
			'basePath'   => '/var/www/themes/amalfi',
		);
		$args     = array_merge( $defaults, $overrides );

		return new ThemeConfig(
			$args['name'],
			$args['slug'],
			$args['version'],
			$args['author'],
			$args['textDomain'],
			$args['basePath'],
		);
	}

	public function test_exposes_the_constructor_values(): void {
		$config = $this->config();

		$this->assertSame( 'Amalfi', $config->name() );
		$this->assertSame( 'amalfi', $config->slug() );
		$this->assertSame( '1.0.0', $config->version() );
		$this->assertSame( 'ThemeZee', $config->author() );
		$this->assertSame( 'amalfi', $config->textDomain() );
		$this->assertSame( '/var/www/themes/amalfi', $config->basePath() );
	}

	public function test_defaults_the_store_and_item_from_constants(): void {
		$config = $this->config();

		$this->assertSame( ThemeConfig::ITEM_ID, $config->itemId() );
		$this->assertSame( ThemeConfig::STORE_URL, $config->storeUrl() );
	}

	public function test_overrides_store_and_item_when_provided(): void {
		$config = new ThemeConfig(
			'Amalfi',
			'amalfi',
			'1.0.0',
			'ThemeZee',
			'amalfi',
			'/base',
			'A theme',
			'https://example.com',
			4242,
			'https://store.example.com',
		);

		$this->assertSame( 4242, $config->itemId() );
		$this->assertSame( 'https://store.example.com', $config->storeUrl() );
		$this->assertSame( 'A theme', $config->description() );
		$this->assertSame( 'https://example.com', $config->uri() );
	}

	public function test_fixed_identifiers(): void {
		$config = $this->config();

		$this->assertSame( 'amalfi_theme_settings', $config->optionName() );
		$this->assertSame( 'amalfi/v1', $config->restNamespace() );
		$this->assertSame( 'amalfi-theme', $config->pageSlug() );
		$this->assertSame( 'manage_options', $config->capability() );
	}

	public function test_page_url_builds_on_the_admin_themes_screen(): void {
		Functions\expect( 'admin_url' )
			->once()
			->with( 'themes.php?page=amalfi-theme' )
			->andReturn( 'https://site.test/wp-admin/themes.php?page=amalfi-theme' );

		$this->assertSame(
			'https://site.test/wp-admin/themes.php?page=amalfi-theme',
			$this->config()->pageUrl()
		);
	}
}
