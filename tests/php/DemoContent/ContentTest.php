<?php
/**
 * Tests for the demo Content provider.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Tests\DemoContent;

use ThemeZee\Amalfi\DemoContent\Content;
use ThemeZee\Amalfi\Tests\TestCase;

final class ContentTest extends TestCase {

	private function content(): Content {
		// A base path that does not exist: pattern/post markup reads resolve
		// to empty strings, which is all these tests need.
		return new Content( $this->themeConfig( array( 'basePath' => '/no/such/theme' ) ) );
	}

	public function test_category_and_tag_names(): void {
		$content = $this->content();

		$this->assertCount( 5, $content->categoryNames() );
		$this->assertContains( 'Destinations', $content->categoryNames() );
		$this->assertCount( 16, $content->tagNames() );
		$this->assertContains( 'positano', $content->tagNames() );
	}

	public function test_post_definitions(): void {
		$definitions = $this->content()->postDefinitions();

		$this->assertCount( 12, $definitions );

		foreach ( $definitions as $definition ) {
			$this->assertArrayHasKey( 'title', $definition );
			$this->assertArrayHasKey( 'category', $definition );
			$this->assertArrayHasKey( 'file', $definition );
			$this->assertArrayHasKey( 'image', $definition );
			$this->assertIsArray( $definition['tags'] );
		}
	}

	public function test_image_files_are_unique(): void {
		$images = $this->content()->imageFiles();

		$this->assertSame( array_values( array_unique( $images ) ), $images );
		$this->assertContains( 'positano-coast.jpg', $images );
	}

	public function test_image_path_is_built_under_the_theme_assets(): void {
		$content = new Content( $this->themeConfig( array( 'basePath' => '/theme' ) ) );

		$this->assertSame(
			'/theme/assets/images/positano-coast.jpg',
			$content->imagePath( 'positano-coast.jpg' )
		);
	}

	public function test_posts_carry_content_loaded_from_markup_files(): void {
		$posts = $this->content()->posts();

		$this->assertCount( 12, $posts );
		// The markup files are not readable from the fake base path, so content
		// resolves to an empty string rather than failing.
		$this->assertSame( '', $posts[0]['content'] );
		$this->assertArrayHasKey( 'excerpt', $posts[0] );
	}

	public function test_home_and_about_pages_are_strings(): void {
		$content = $this->content();

		$this->assertSame( '', $content->homePage() );
		$this->assertSame( '', $content->aboutPage() );
	}
}
