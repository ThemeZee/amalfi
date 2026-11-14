<?php
/**
 * Tests for the demo content REST controller.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Tests\DemoContent;

use Brain\Monkey\Functions;
use Mockery;
use ThemeZee\Amalfi\DemoContent\Importer;
use ThemeZee\Amalfi\DemoContent\RestController;
use ThemeZee\Amalfi\Tests\TestCase;
use WP_REST_Request;

final class RestControllerTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Functions\when( 'wp_list_filter' )->alias(
			static function ( $list, $args ) {
				return array_values(
					array_filter(
						$list,
						static function ( $item ) use ( $args ) {
							foreach ( $args as $key => $value ) {
								if ( ( $item[ $key ] ?? null ) !== $value ) {
									return false;
								}
							}
							return true;
						}
					)
				);
			}
		);
	}

	private function importer(): Mockery\MockInterface {
		$importer = Mockery::mock( Importer::class );
		$importer->shouldReceive( 'parts' )->andReturn(
			array(
				array( 'key' => 'posts', 'label' => 'Posts', 'count' => 2 ),
				array( 'key' => 'pages', 'label' => 'Pages', 'count' => 3 ),
			)
		);

		return $importer;
	}

	public function test_import_normalises_requested_parts_and_returns_results(): void {
		$importer = $this->importer();
		$importer->shouldReceive( 'run' )
			->once()
			->with( array( 'posts' => true, 'pages' => false ) )
			->andReturn(
				array(
					array( 'task' => 'posts', 'status' => 'success', 'message' => 'ok' ),
				)
			);

		$controller = new RestController( $this->themeConfig(), $importer );
		$request    = new WP_REST_Request( array( 'parts' => array( 'posts' => true ) ) );

		$response = $controller->import( $request );
		$data     = $response->get_data();

		$this->assertCount( 1, $data['results'] );
		$this->assertFalse( $data['failed'] );
	}

	public function test_import_flags_failure_when_a_task_errors(): void {
		$importer = $this->importer();
		$importer->shouldReceive( 'run' )->andReturn(
			array(
				array( 'task' => 'posts', 'status' => 'error', 'message' => 'bad' ),
			)
		);

		$controller = new RestController( $this->themeConfig(), $importer );
		$request    = new WP_REST_Request(
			array( 'parts' => array( 'posts' => true, 'pages' => true ) )
		);

		$this->assertTrue( $controller->import( $request )->get_data()['failed'] );
	}

	public function test_permission_check_uses_the_configured_capability(): void {
		Functions\expect( 'current_user_can' )
			->once()
			->with( 'manage_options' )
			->andReturn( true );

		$controller = new RestController( $this->themeConfig(), $this->importer() );

		$this->assertTrue( $controller->checkPermission() );
	}
}
