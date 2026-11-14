<?php
/**
 * Tests for the ThemeUpdater.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Tests\Updates;

use Brain\Monkey\Functions;
use Mockery;
use stdClass;
use ThemeZee\Amalfi\Core\SettingsRepository;
use ThemeZee\Amalfi\Tests\TestCase;
use ThemeZee\Amalfi\Updates\ThemeUpdater;
use WP_Error;

final class ThemeUpdaterTest extends TestCase {

	private function updater( string $version = '1.0.0' ): ThemeUpdater {
		$settings = Mockery::mock( SettingsRepository::class );
		$settings->shouldReceive( 'licenseKey' )->andReturn( 'key' );

		return new ThemeUpdater(
			$this->themeConfig( array( 'version' => $version, 'slug' => 'amalfi' ) ),
			$settings
		);
	}

	private function versionResponse( string $newVersion ): stdClass {
		$data              = new stdClass();
		$data->new_version = $newVersion;

		return $data;
	}

	public function test_inject_update_adds_a_newer_version_to_the_response(): void {
		Functions\when( 'get_transient' )->justReturn( $this->versionResponse( '2.0.0' ) );

		$transient = ( $this->updater( '1.0.0' ) )->injectUpdate( new stdClass() );

		$this->assertArrayHasKey( 'amalfi', $transient->response );
		$this->assertSame( '2.0.0', $transient->response['amalfi']['new_version'] );
		$this->assertSame( 'amalfi', $transient->response['amalfi']['theme'] );
	}

	public function test_inject_update_lists_an_up_to_date_theme_as_no_update(): void {
		Functions\when( 'get_transient' )->justReturn( $this->versionResponse( '1.0.0' ) );

		$transient = ( $this->updater( '1.0.0' ) )->injectUpdate( new stdClass() );

		$this->assertArrayHasKey( 'amalfi', $transient->no_update );
		$this->assertObjectNotHasProperty( 'response', $transient );
	}

	public function test_inject_update_leaves_the_transient_alone_when_the_fetch_fails(): void {
		// No cached object, and the remote request errors: a failure marker is
		// cached and the transient is returned unchanged.
		Functions\when( 'get_transient' )->justReturn( false );
		Functions\when( 'wp_remote_post' )->justReturn( new WP_Error( 'http', 'down' ) );
		Functions\when( 'is_wp_error' )->alias(
			static fn( $thing ) => $thing instanceof WP_Error
		);
		Functions\when( 'get_bloginfo' )->justReturn( '7.0' );
		Functions\expect( 'set_transient' )->once();

		$input     = new stdClass();
		$transient = ( $this->updater( '1.0.0' ) )->injectUpdate( $input );

		$this->assertSame( $input, $transient );
		$this->assertObjectNotHasProperty( 'response', $transient );
		$this->assertObjectNotHasProperty( 'no_update', $transient );
	}

	public function test_flush_deletes_the_cached_response(): void {
		Functions\expect( 'delete_transient' )
			->once()
			->with( 'amalfi-update-response' );

		( $this->updater() )->flush();
	}
}
