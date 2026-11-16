<?php
/**
 * Tests for the demo Content provider.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Tests\DemoContent;

use Brain\Monkey\Functions;
use ThemeZee\Amalfi\DemoContent\Content;
use ThemeZee\Amalfi\Tests\TestCase;

final class ContentTest extends TestCase {

	/**
	 * Temp directories created for page-markup fixtures, removed on teardown.
	 *
	 * @var string[]
	 */
	private array $tmpDirs = array();

	protected function tearDown(): void {
		foreach ( $this->tmpDirs as $dir ) {
			$this->removeDir( $dir );
		}
		$this->tmpDirs = array();

		parent::tearDown();
	}

	private function content(): Content {
		// A base path that does not exist: pattern/post markup reads resolve
		// to empty strings, which is all these tests need.
		return new Content( $this->themeConfig( array( 'basePath' => '/no/such/theme' ) ) );
	}

	/**
	 * Create a temp theme base holding a single demo page-markup fixture.
	 *
	 * @param string $file   Page file base name (without extension).
	 * @param string $markup The fixture markup.
	 * @return string The temp base path to pass as the theme base path.
	 */
	private function withPageFixture( string $file, string $markup ): string {
		$base = sys_get_temp_dir() . '/amalfi-test-' . uniqid();
		$dir  = $base . '/demo-content/pages';
		mkdir( $dir, 0777, true );
		file_put_contents( $dir . '/' . $file . '.html', $markup );
		$this->tmpDirs[] = $base;

		return $base;
	}

	/**
	 * Recursively remove a directory tree.
	 *
	 * @param string $dir The directory to remove.
	 */
	private function removeDir( string $dir ): void {
		if ( ! is_dir( $dir ) ) {
			return;
		}

		foreach ( scandir( $dir ) as $entry ) {
			if ( '.' === $entry || '..' === $entry ) {
				continue;
			}

			$path = $dir . '/' . $entry;
			is_dir( $path ) ? $this->removeDir( $path ) : unlink( $path );
		}

		rmdir( $dir );
	}

	/**
	 * Find a page/definition entry by its title.
	 *
	 * @param array<int, array<string, mixed>> $entries The entries to search.
	 * @param string                           $title   The title to match.
	 * @return array<string, mixed>
	 */
	private function entryByTitle( array $entries, string $title ): array {
		foreach ( $entries as $entry ) {
			if ( ( $entry['title'] ?? null ) === $title ) {
				return $entry;
			}
		}

		$this->fail( "No entry titled {$title}." );
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

	public function test_page_definitions(): void {
		$definitions = $this->content()->pageDefinitions();

		$this->assertCount( 6, $definitions );

		$titles = array_column( $definitions, 'title' );
		foreach ( array( 'Home', 'Activities', 'Destinations', 'About', 'Blog', 'Contact' ) as $expected ) {
			$this->assertContains( $expected, $titles );
		}

		foreach ( $definitions as $definition ) {
			$this->assertArrayHasKey( 'file', $definition );
			$this->assertArrayHasKey( 'template', $definition );
			$this->assertIsBool( $definition['front'] );
			$this->assertIsBool( $definition['posts'] );
		}

		// Exactly one front page and exactly one posts page.
		$this->assertCount( 1, array_filter( $definitions, static fn ( $d ) => $d['front'] ) );
		$this->assertCount( 1, array_filter( $definitions, static fn ( $d ) => $d['posts'] ) );

		// The blog index is the posts page and carries no content file.
		$blog = $this->entryByTitle( $definitions, 'Blog' );
		$this->assertTrue( $blog['posts'] );
		$this->assertSame( '', $blog['file'] );

		// The home page is the front page and uses the title-less template.
		$home = $this->entryByTitle( $definitions, 'Home' );
		$this->assertTrue( $home['front'] );
		$this->assertSame( 'page-no-title', $home['template'] );
	}

	public function test_pages_resolve_to_empty_content_when_files_are_missing(): void {
		$pages = $this->content()->pages();

		$this->assertCount( 6, $pages );

		foreach ( $pages as $page ) {
			// The markup files are not readable from the fake base path, so
			// content resolves to an empty string rather than failing.
			$this->assertSame( '', $page['content'] );
			$this->assertArrayHasKey( 'template', $page );
		}
	}

	public function test_pages_resolve_the_home_url_placeholder(): void {
		Functions\when( 'home_url' )->justReturn( 'https://example.test' );
		Functions\when( 'untrailingslashit' )->alias(
			static fn ( $value ) => rtrim( (string) $value, '/' )
		);

		$base    = $this->withPageFixture( 'activities', '<a href="%HOME_URL%/blog/">Blog</a> and %HOME_URL%/category/x/' );
		$content = new Content( $this->themeConfig( array( 'basePath' => $base ) ) );

		$activities = $this->entryByTitle( $content->pages(), 'Activities' );

		$this->assertStringContainsString( 'href="https://example.test/blog/"', $activities['content'] );
		$this->assertStringContainsString( 'https://example.test/category/x/', $activities['content'] );
		$this->assertStringNotContainsString( '%HOME_URL%', $activities['content'] );
	}

	/**
	 * The placeholder replacement is a plain data substitution: only the known
	 * token is swapped, and everything else — including markup that looks like
	 * code or like another token — is preserved verbatim and never executed.
	 */
	public function test_placeholder_replacement_treats_markup_as_inert_data(): void {
		Functions\when( 'home_url' )->justReturn( 'https://example.test' );
		Functions\when( 'untrailingslashit' )->alias(
			static fn ( $value ) => rtrim( (string) $value, '/' )
		);

		$hostile = '%HOME_URL%/ok/ <?php system("id"); ?> <script>x()</script> %UNKNOWN_TOKEN% ${injected}';
		$base    = $this->withPageFixture( 'about', $hostile );
		$content = new Content( $this->themeConfig( array( 'basePath' => $base ) ) );

		$result = $this->entryByTitle( $content->pages(), 'About' )['content'];

		// Known token resolved…
		$this->assertStringContainsString( 'https://example.test/ok/', $result );
		$this->assertStringNotContainsString( '%HOME_URL%', $result );
		// …and nothing else touched: code-like and unknown-token text stays literal.
		$this->assertStringContainsString( '<?php system("id"); ?>', $result );
		$this->assertStringContainsString( '<script>x()</script>', $result );
		$this->assertStringContainsString( '%UNKNOWN_TOKEN%', $result );
		$this->assertStringContainsString( '${injected}', $result );
	}

	/**
	 * Guards the convention: the bundled page files must parameterise internal
	 * links with %HOME_URL% rather than hardcoding a path or the demo sub-site.
	 */
	public function test_shipped_page_files_have_no_hardcoded_internal_links(): void {
		$dir = dirname( __DIR__, 3 ) . '/demo-content/pages';

		$files = glob( $dir . '/*.html' );
		$this->assertNotEmpty( $files, 'No bundled demo page files were found.' );

		foreach ( $files as $file ) {
			$markup = (string) file_get_contents( $file );
			$name   = basename( $file );

			$this->assertStringNotContainsString( 'href="/', $markup, "{$name} contains a hardcoded root-relative link." );
			$this->assertStringNotContainsString( '/amalfi/', $markup, "{$name} leaks the demo sub-site path." );

			// Query Loops must not pin a hardcoded term ID: those IDs do not
			// exist on a fresh install, so the loop would render no posts.
			$this->assertStringNotContainsString( 'taxQuery', $markup, "{$name} pins a Query Loop to a hardcoded taxonomy term." );
		}
	}
}
