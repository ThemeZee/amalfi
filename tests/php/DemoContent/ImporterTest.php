<?php
/**
 * Tests for the demo content Importer.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Tests\DemoContent;

use Brain\Monkey\Functions;
use Mockery;
use ThemeZee\Amalfi\DemoContent\Content;
use ThemeZee\Amalfi\DemoContent\Importer;
use ThemeZee\Amalfi\Tests\TestCase;
use WP_Error;

final class ImporterTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();

		// Media helpers are considered loaded so the importer does not try to
		// require WordPress admin includes.
		Functions\when( 'media_handle_sideload' )->justReturn( 1 );
		Functions\when( 'wp_list_pluck' )->alias(
			static fn( $list, $field, $index = null ) => null === $index
				? array_column( $list, $field )
				: array_column( $list, $field, $index )
		);
		Functions\when( 'is_wp_error' )->alias(
			static fn( $thing ) => $thing instanceof WP_Error
		);
	}

	private function content(): Mockery\MockInterface {
		$content = Mockery::mock( Content::class );
		$content->shouldReceive( 'postDefinitions' )->andReturn(
			array_fill( 0, 4, array() )
		);
		$content->shouldReceive( 'categoryNames' )->andReturn(
			array( 'Alpha', 'Beta' )
		);
		$content->shouldReceive( 'tagNames' )->andReturn( array( 'one' ) );
		$content->shouldReceive( 'imageFiles' )->andReturn(
			array( 'a.jpg', 'b.jpg', 'c.jpg' )
		);
		$content->shouldReceive( 'pageDefinitions' )->andReturn(
			array_fill( 0, 6, array() )
		);

		return $content;
	}

	public function test_parts_report_each_importable_type_with_counts(): void {
		$parts = ( new Importer( $this->content() ) )->parts();

		$byKey = array_column( $parts, 'count', 'key' );

		$this->assertSame( 4, $byKey['posts'] );
		$this->assertSame( 2, $byKey['categories'] );
		$this->assertSame( 1, $byKey['tags'] );
		$this->assertSame( 3, $byKey['image'] );
		$this->assertSame( 6, $byKey['pages'] );
	}

	public function test_run_does_nothing_when_no_parts_are_selected(): void {
		$results = ( new Importer( $this->content() ) )->run(
			array(
				'posts'      => false,
				'categories' => false,
				'tags'       => false,
				'image'      => false,
				'pages'      => false,
			)
		);

		$this->assertSame( array(), $results );
	}

	public function test_run_imports_categories_successfully(): void {
		Functions\when( 'term_exists' )->justReturn( null );
		Functions\when( 'wp_insert_term' )->justReturn( array( 'term_id' => 7 ) );

		$results = ( new Importer( $this->content() ) )->run(
			array( 'categories' => true )
		);

		$this->assertCount( 1, $results );
		$this->assertSame( 'categories', $results[0]['task'] );
		$this->assertSame( 'success', $results[0]['status'] );
	}

	public function test_run_records_a_failed_task_without_aborting(): void {
		Functions\when( 'term_exists' )->justReturn( null );
		// Inserting a term fails, so the categories task is recorded as an error.
		Functions\when( 'wp_insert_term' )->justReturn(
			new WP_Error( 'insert', 'nope' )
		);

		$results = ( new Importer( $this->content() ) )->run(
			array( 'categories' => true )
		);

		$this->assertCount( 1, $results );
		$this->assertSame( 'categories', $results[0]['task'] );
		$this->assertSame( 'error', $results[0]['status'] );
	}

	public function test_run_imports_pages_with_template_slug_and_front_posts_config(): void {
		$content = $this->content();
		$content->shouldReceive( 'pages' )->andReturn(
			array(
				array(
					'title'    => 'Home',
					'content'  => 'home markup',
					'template' => 'page-no-title',
					'front'    => true,
					'posts'    => false,
				),
				array(
					'title'    => 'Blog',
					'content'  => '',
					'template' => '',
					'front'    => false,
					'posts'    => true,
				),
			)
		);

		$inserted = array();
		Functions\when( 'get_current_user_id' )->justReturn( 1 );
		Functions\when( 'wp_insert_post' )->alias(
			static function ( $postarr ) use ( &$inserted ) {
				$inserted[] = $postarr;

				return count( $inserted ); // Return ascending IDs 1, 2, …
			}
		);

		$options = array();
		Functions\when( 'update_option' )->alias(
			static function ( $key, $value ) use ( &$options ) {
				$options[ $key ] = $value;

				return true;
			}
		);

		$results = ( new Importer( $content ) )->run( array( 'pages' => true ) );

		$this->assertSame( 'pages', $results[0]['task'] );
		$this->assertSame( 'success', $results[0]['status'] );

		// Regression: the template is passed as the block-template slug, never a
		// file name — `wp_insert_post` rejects `page-no-title.html`.
		$this->assertSame( 'page-no-title', $inserted[0]['page_template'] );
		// A page without a template sets no page_template key at all.
		$this->assertArrayNotHasKey( 'page_template', $inserted[1] );

		// The front and posts pages are wired to the pages that declared them.
		$this->assertSame( 'page', $options['show_on_front'] );
		$this->assertSame( 1, $options['page_on_front'] );
		$this->assertSame( 2, $options['page_for_posts'] );
	}
}
