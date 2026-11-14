<?php
/**
 * Tests for SettingsRepository.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Tests\Core;

use Brain\Monkey\Functions;
use ThemeZee\Amalfi\Core\SettingsRepository;
use ThemeZee\Amalfi\Tests\TestCase;

final class SettingsRepositoryTest extends TestCase {

	private const OPTION = 'amalfi_theme_settings';

	protected function setUp(): void {
		parent::setUp();

		// wp_parse_args merges the stored values over the defaults.
		Functions\when( 'wp_parse_args' )->alias(
			static fn( $args, $defaults ) => array_merge( $defaults, (array) $args )
		);
	}

	private function repository(): SettingsRepository {
		return new SettingsRepository( $this->themeConfig() );
	}

	public function test_all_merges_stored_values_over_defaults(): void {
		Functions\expect( 'get_option' )
			->once()
			->with( self::OPTION, array() )
			->andReturn( array( 'amalfi_license_key' => 'abc123' ) );

		$all = $this->repository()->all();

		$this->assertSame( 'abc123', $all['amalfi_license_key'] );
		$this->assertSame(
			SettingsRepository::STATUS_INACTIVE,
			$all['amalfi_license_status']
		);
	}

	public function test_all_falls_back_to_defaults_when_option_is_not_an_array(): void {
		Functions\expect( 'get_option' )->once()->andReturn( false );

		$all = $this->repository()->all();

		$this->assertSame(
			array(
				'amalfi_license_key'    => '',
				'amalfi_license_status' => SettingsRepository::STATUS_INACTIVE,
			),
			$all
		);
	}

	public function test_save_merges_over_the_stored_values(): void {
		Functions\expect( 'get_option' )
			->once()
			->andReturn( array( 'amalfi_license_key' => 'old' ) );

		Functions\expect( 'update_option' )
			->once()
			->with(
				self::OPTION,
				array(
					'amalfi_license_key'    => 'new',
					'amalfi_license_status' => 'valid',
				)
			);

		$this->repository()->save(
			array(
				'amalfi_license_key'    => 'new',
				'amalfi_license_status' => 'valid',
			)
		);
	}

	public function test_license_key_is_trimmed(): void {
		Functions\expect( 'get_option' )
			->once()
			->andReturn( array( 'amalfi_license_key' => '  spaced  ' ) );

		$this->assertSame( 'spaced', $this->repository()->licenseKey() );
	}

	public function test_has_valid_license_requires_valid_status_and_a_key(): void {
		Functions\when( 'get_option' )->justReturn(
			array(
				'amalfi_license_key'    => 'key',
				'amalfi_license_status' => SettingsRepository::STATUS_VALID,
			)
		);

		$this->assertTrue( $this->repository()->hasValidLicense() );
	}

	public function test_has_valid_license_is_false_without_a_key(): void {
		Functions\when( 'get_option' )->justReturn(
			array(
				'amalfi_license_key'    => '',
				'amalfi_license_status' => SettingsRepository::STATUS_VALID,
			)
		);

		$this->assertFalse( $this->repository()->hasValidLicense() );
	}

	public function test_store_license_persists_key_and_status(): void {
		Functions\when( 'get_option' )->justReturn( array() );

		Functions\expect( 'update_option' )
			->once()
			->with(
				self::OPTION,
				array(
					'amalfi_license_key'    => 'the-key',
					'amalfi_license_status' => SettingsRepository::STATUS_VALID,
				)
			);

		$this->repository()->storeLicense( 'the-key', SettingsRepository::STATUS_VALID );
	}

	public function test_clear_license_resets_key_and_status(): void {
		Functions\when( 'get_option' )->justReturn(
			array( 'amalfi_license_key' => 'the-key' )
		);

		Functions\expect( 'update_option' )
			->once()
			->with(
				self::OPTION,
				array(
					'amalfi_license_key'    => '',
					'amalfi_license_status' => SettingsRepository::STATUS_INACTIVE,
				)
			);

		$this->repository()->clearLicense();
	}
}
