<?php
/**
 * Tests for block pattern category registration.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Tests\Setup;

use Brain\Monkey\Functions;
use Mockery;
use ThemeZee\Amalfi\Setup\BlockPatternCategory;
use ThemeZee\Amalfi\Tests\TestCase;
use WP_Block_Pattern_Categories_Registry;

final class BlockPatternCategoryTest extends TestCase {

	protected function tearDown(): void {
		WP_Block_Pattern_Categories_Registry::$instance = null;
		parent::tearDown();
	}

	public function test_registers_every_category_that_is_not_yet_registered(): void {
		$registry = Mockery::mock();
		$registry->shouldReceive( 'is_registered' )->andReturn( false );
		WP_Block_Pattern_Categories_Registry::$instance = $registry;

		$registered = array();
		Functions\when( 'register_block_pattern_category' )->alias(
			static function ( $name, $properties ) use ( &$registered ) {
				$registered[ $name ] = $properties['label'];
			}
		);

		( new BlockPatternCategory() )();

		$this->assertArrayHasKey( 'amalfi_hero', $registered );
		$this->assertArrayHasKey( 'amalfi_query_loops', $registered );
		$this->assertCount( 8, $registered );
		$this->assertSame( 'Amalfi: Hero', $registered['amalfi_hero'] );
	}

	public function test_skips_categories_that_are_already_registered(): void {
		$registry = Mockery::mock();
		$registry->shouldReceive( 'is_registered' )->andReturn( true );
		WP_Block_Pattern_Categories_Registry::$instance = $registry;

		Functions\expect( 'register_block_pattern_category' )->never();

		( new BlockPatternCategory() )();
	}
}
