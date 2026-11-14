<?php
/**
 * Tests for the license REST controller.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Tests\Licensing;

use Brain\Monkey\Functions;
use Mockery;
use ThemeZee\Amalfi\Licensing\LicenseClient;
use ThemeZee\Amalfi\Licensing\RestController;
use ThemeZee\Amalfi\Tests\TestCase;
use WP_REST_Request;

final class RestControllerTest extends TestCase {

	public function test_manage_activates_with_the_sanitised_key(): void {
		$license = Mockery::mock( LicenseClient::class );
		$license->shouldReceive( 'activate' )
			->once()
			->with( 'my-key' )
			->andReturn( array( 'type' => 'success', 'message' => 'done' ) );
		$license->shouldNotReceive( 'deactivate' );

		$controller = new RestController( $this->themeConfig(), $license );
		$request    = new WP_REST_Request(
			array( 'action' => 'activate', 'key' => 'my-key' )
		);

		$this->assertSame(
			array( 'type' => 'success', 'message' => 'done' ),
			$controller->manage( $request )->get_data()
		);
	}

	public function test_manage_deactivates_without_a_key(): void {
		$license = Mockery::mock( LicenseClient::class );
		$license->shouldReceive( 'deactivate' )
			->once()
			->andReturn( array( 'type' => 'success', 'message' => 'off' ) );
		$license->shouldNotReceive( 'activate' );

		$controller = new RestController( $this->themeConfig(), $license );
		$request    = new WP_REST_Request( array( 'action' => 'deactivate' ) );

		$this->assertSame(
			array( 'type' => 'success', 'message' => 'off' ),
			$controller->manage( $request )->get_data()
		);
	}

	public function test_permission_check_uses_the_configured_capability(): void {
		Functions\expect( 'current_user_can' )
			->once()
			->with( 'manage_options' )
			->andReturn( false );

		$license    = Mockery::mock( LicenseClient::class );
		$controller = new RestController( $this->themeConfig(), $license );

		$this->assertFalse( $controller->checkPermission() );
	}
}
