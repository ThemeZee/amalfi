<?php
/**
 * Tests for LicenseClient.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Tests\Licensing;

use Brain\Monkey\Functions;
use Mockery;
use ThemeZee\Amalfi\Core\SettingsRepository;
use ThemeZee\Amalfi\Core\ThemeConfig;
use ThemeZee\Amalfi\Licensing\LicenseClient;
use ThemeZee\Amalfi\Tests\TestCase;
use WP_Error;

final class LicenseClientTest extends TestCase {

	private function config(): ThemeConfig {
		return $this->themeConfig();
	}

	protected function setUp(): void {
		parent::setUp();
		Functions\when( 'home_url' )->justReturn( 'https://site.test' );
		// Treat only real WP_Error instances as errors.
		Functions\when( 'is_wp_error' )->alias(
			static fn( $thing ) => $thing instanceof WP_Error
		);
	}

	public function test_state_masks_all_but_the_last_five_characters(): void {
		$settings = Mockery::mock( SettingsRepository::class );
		$settings->shouldReceive( 'licenseKey' )->andReturn( 'ABCDEFGH' );
		$settings->shouldReceive( 'hasValidLicense' )->andReturn( true );
		$settings->shouldReceive( 'licenseStatus' )->andReturn( 'valid' );

		$state = ( new LicenseClient( $this->config(), $settings ) )->state();

		$this->assertTrue( $state['active'] );
		$this->assertSame( 'valid', $state['status'] );
		$this->assertSame( '***DEFGH', $state['keyMasked'] );
	}

	public function test_state_has_no_masked_key_when_empty(): void {
		$settings = Mockery::mock( SettingsRepository::class );
		$settings->shouldReceive( 'licenseKey' )->andReturn( '' );
		$settings->shouldReceive( 'hasValidLicense' )->andReturn( false );
		$settings->shouldReceive( 'licenseStatus' )->andReturn( 'inactive' );

		$state = ( new LicenseClient( $this->config(), $settings ) )->state();

		$this->assertFalse( $state['active'] );
		$this->assertSame( '', $state['keyMasked'] );
	}

	public function test_activate_rejects_an_empty_key(): void {
		$settings = $this->inactiveSettings();
		$settings->shouldNotReceive( 'storeLicense' );

		$result = ( new LicenseClient( $this->config(), $settings ) )->activate( '   ' );

		$this->assertSame( 'error', $result['type'] );
		$this->assertSame( 'Please enter a license key.', $result['message'] );
	}

	public function test_activate_reports_a_transport_error(): void {
		Functions\when( 'wp_remote_post' )->justReturn( new WP_Error( 'http', 'boom' ) );

		$settings = $this->inactiveSettings();
		$settings->shouldNotReceive( 'storeLicense' );

		$result = ( new LicenseClient( $this->config(), $settings ) )->activate( 'key' );

		$this->assertSame( 'error', $result['type'] );
		$this->assertSame( 'An error occurred, please try again.', $result['message'] );
	}

	public function test_activate_maps_an_api_error_message(): void {
		Functions\when( 'wp_remote_post' )->justReturn( array( 'body' => '' ) );
		Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 200 );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn(
			'{"success":false,"error":"missing"}'
		);

		$settings = $this->inactiveSettings();
		$settings->shouldNotReceive( 'storeLicense' );

		$result = ( new LicenseClient( $this->config(), $settings ) )->activate( 'key' );

		$this->assertSame( 'error', $result['type'] );
		$this->assertSame( 'Invalid license key.', $result['message'] );
	}

	public function test_activate_stores_the_license_on_success(): void {
		Functions\when( 'wp_remote_post' )->justReturn( array( 'body' => '' ) );
		Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 200 );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn( '{"success":true}' );

		$settings = $this->inactiveSettings();
		$settings->shouldReceive( 'storeLicense' )
			->once()
			->with( 'the-key', SettingsRepository::STATUS_VALID );

		$result = ( new LicenseClient( $this->config(), $settings ) )->activate( ' the-key ' );

		$this->assertSame( 'success', $result['type'] );
		$this->assertSame( 'License was successfully activated.', $result['message'] );
	}

	public function test_deactivate_clears_the_license(): void {
		Functions\when( 'wp_remote_post' )->justReturn( array( 'body' => '' ) );
		Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 200 );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn( '{"success":true}' );

		$settings = Mockery::mock( SettingsRepository::class );
		$settings->shouldReceive( 'licenseKey' )->andReturn( 'stored-key' );
		$settings->shouldReceive( 'licenseStatus' )->andReturn( 'inactive' );
		$settings->shouldReceive( 'hasValidLicense' )->andReturn( false );
		$settings->shouldReceive( 'clearLicense' )->once();

		$result = ( new LicenseClient( $this->config(), $settings ) )->deactivate();

		$this->assertSame( 'success', $result['type'] );
		$this->assertSame( 'License was deactivated.', $result['message'] );
	}

	private function inactiveSettings(): Mockery\MockInterface {
		$settings = Mockery::mock( SettingsRepository::class );
		$settings->shouldReceive( 'licenseKey' )->andReturn( '' );
		$settings->shouldReceive( 'licenseStatus' )->andReturn( 'inactive' );
		$settings->shouldReceive( 'hasValidLicense' )->andReturn( false );

		return $settings;
	}
}
