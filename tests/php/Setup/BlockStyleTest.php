<?php
/**
 * Tests for block style registration.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Tests\Setup;

use Brain\Monkey\Functions;
use ThemeZee\Amalfi\Setup\BlockStyle;
use ThemeZee\Amalfi\Tests\TestCase;

final class BlockStyleTest extends TestCase {

	public function test_registers_each_block_style(): void {
		$registered = array();
		Functions\when( 'register_block_style' )->alias(
			static function ( $block, $properties ) use ( &$registered ) {
				$registered[] = $block . ':' . $properties['name'];
			}
		);

		( new BlockStyle() )();

		$this->assertSame(
			array(
				'core/navigation:main-navigation',
				'core/paragraph:overline',
				'core/heading:overline',
				'core/social-links:primary',
				'core/social-links:outline',
				'core/post-terms:outline',
			),
			$registered
		);
	}
}
