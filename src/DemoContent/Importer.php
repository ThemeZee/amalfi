<?php
/**
 * Demo content importer.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\DemoContent;

use Throwable;

/**
 * Imports the demo content (categories, tags, featured images, posts and pages).
 *
 * The importer exposes a single {@see self::run()} operation that performs the
 * selected parts and returns a structured result, so it can be driven from a
 * REST endpoint (or anywhere else) rather than a form submission.
 */
class Importer {

	/**
	 * Constructor.
	 *
	 * @param Content $content The demo content provider.
	 */
	public function __construct(
		private readonly Content $content,
	) {
	}

	/**
	 * The content parts that can be imported, with a label and item count.
	 *
	 * @return array<int, array{key: string, label: string, count: int}>
	 */
	public function parts(): array {
		return array(
			array(
				'key'   => 'posts',
				'label' => __( 'Blog Posts', 'amalfi' ),
				'count' => count( $this->content->postDefinitions() ),
			),
			array(
				'key'   => 'categories',
				'label' => __( 'Categories', 'amalfi' ),
				'count' => count( $this->content->categoryNames() ),
			),
			array(
				'key'   => 'tags',
				'label' => __( 'Tags', 'amalfi' ),
				'count' => count( $this->content->tagNames() ),
			),
			array(
				'key'   => 'image',
				'label' => __( 'Featured Images', 'amalfi' ),
				'count' => count( $this->content->imageFiles() ),
			),
			array(
				'key'   => 'pages',
				'label' => __( 'Static Pages', 'amalfi' ),
				'count' => 3,
			),
		);
	}

	/**
	 * Import the selected demo content parts.
	 *
	 * Categories, tags and images are imported first so posts can be linked to
	 * them. Each part is independent: a failure in one is recorded and the rest
	 * still run. Posts are always created with their title, content and excerpt;
	 * categories, tags and featured images are only attached when available.
	 *
	 * @param array<string, bool> $parts Which parts to import, keyed as in {@see self::parts()}.
	 * @return array<int, array{task: string, label: string, status: string, message: string}> The per-part results.
	 */
	public function run( array $parts ): array {
		$this->ensureMediaFunctions();

		$labels  = wp_list_pluck( $this->parts(), 'label', 'key' );
		$results = array();

		$categoryMap = ! empty( $parts['categories'] )
			? $this->runTask( $results, 'categories', $labels['categories'], fn () => $this->importCategories(), array() )
			: array();

		$tagMap = ! empty( $parts['tags'] )
			? $this->runTask( $results, 'tags', $labels['tags'], fn () => $this->importTags(), array() )
			: array();

		$imageMap = ! empty( $parts['image'] )
			? $this->runTask( $results, 'image', $labels['image'], fn () => $this->importFeaturedImages(), array() )
			: array();

		if ( ! empty( $parts['posts'] ) ) {
			$this->runTask(
				$results,
				'posts',
				$labels['posts'],
				function () use ( $categoryMap, $tagMap, $imageMap ): bool {
					$this->importPosts( $categoryMap, $tagMap, $imageMap );
					return true;
				},
				false
			);
		}

		if ( ! empty( $parts['pages'] ) ) {
			$this->runTask(
				$results,
				'pages',
				$labels['pages'],
				function (): bool {
					$this->importPages();
					return true;
				},
				false
			);
		}

		return $results;
	}

	/**
	 * Run a single import task, appending a success or error result.
	 *
	 * @param array<int, array{task: string, label: string, status: string, message: string}> $results  Results, appended to by reference.
	 * @param string                                                                          $task     The task key.
	 * @param string                                                                          $label    The task label.
	 * @param callable                                                                        $op       The operation to run; returns its value.
	 * @param mixed                                                                           $fallback Value returned when the task fails.
	 * @return mixed The operation's value, or the fallback on failure.
	 */
	private function runTask( array &$results, string $task, string $label, callable $op, mixed $fallback ): mixed {
		try {
			$value     = $op();
			$results[] = array(
				'task'    => $task,
				'label'   => $label,
				'status'  => 'success',
				/* translators: %s: the imported content type, e.g. Categories. */
				'message' => sprintf( __( '%s imported successfully.', 'amalfi' ), $label ),
			);

			return $value;
		} catch ( Throwable ) {
			$results[] = array(
				'task'    => $task,
				'label'   => $label,
				'status'  => 'error',
				/* translators: %s: the content type that failed to import, e.g. Categories. */
				'message' => sprintf( __( '%s could not be imported.', 'amalfi' ), $label ),
			);

			return $fallback;
		}
	}

	/**
	 * Create the demo categories.
	 *
	 * @return array<string, int> A map of category name to term ID.
	 * @throws \RuntimeException When a category cannot be created.
	 */
	private function importCategories(): array {
		return $this->importTerms( $this->content->categoryNames(), 'category' );
	}

	/**
	 * Create the demo tags.
	 *
	 * @return array<string, int> A map of tag name to term ID.
	 * @throws \RuntimeException When a tag cannot be created.
	 */
	private function importTags(): array {
		return $this->importTerms( $this->content->tagNames(), 'post_tag' );
	}

	/**
	 * Create terms in a taxonomy, reusing existing terms of the same name.
	 *
	 * Reusing existing terms rather than duplicating them keeps the import safe
	 * to run more than once.
	 *
	 * @param string[] $names    The term names to create.
	 * @param string   $taxonomy The taxonomy the terms belong to.
	 * @return array<string, int> A map of term name to term ID.
	 * @throws \RuntimeException When a term cannot be created.
	 */
	private function importTerms( array $names, string $taxonomy ): array {
		$map = array();

		foreach ( $names as $name ) {
			$existing = term_exists( $name, $taxonomy );

			if ( is_array( $existing ) ) {
				$map[ $name ] = (int) $existing['term_id'];
				continue;
			}

			$term = wp_insert_term( $name, $taxonomy );

			if ( is_wp_error( $term ) ) {
				throw new \RuntimeException( esc_html( $term->get_error_message() ) );
			}

			$map[ $name ] = (int) $term['term_id'];
		}

		return $map;
	}

	/**
	 * Sideload the bundled featured images into the media library.
	 *
	 * @return array<string, int> A map of image file name to attachment ID.
	 * @throws \RuntimeException When an image cannot be imported.
	 */
	private function importFeaturedImages(): array {
		$map = array();

		foreach ( $this->content->imageFiles() as $file ) {
			$map[ $file ] = $this->sideloadImage( $file );
		}

		return $map;
	}

	/**
	 * Sideload a single bundled image into the media library.
	 *
	 * @param string $file The image file name.
	 * @return int The attachment ID.
	 * @throws \RuntimeException When the image cannot be imported.
	 */
	private function sideloadImage( string $file ): int {
		$source = $this->content->imagePath( $file );

		if ( ! is_readable( $source ) ) {
			throw new \RuntimeException( 'Demo image not found: ' . esc_html( $file ) );
		}

		$temp = wp_tempnam( basename( $source ) );
		if ( ! $temp || ! copy( $source, $temp ) ) {
			throw new \RuntimeException( 'Could not copy demo image: ' . esc_html( $file ) );
		}

		$sideload = array(
			'name'     => basename( $source ),
			'tmp_name' => $temp,
		);

		$id = media_handle_sideload( $sideload, 0 );

		if ( is_wp_error( $id ) ) {
			wp_delete_file( $temp );
			throw new \RuntimeException( esc_html( $id->get_error_message() ) );
		}

		return (int) $id;
	}

	/**
	 * Create the demo posts.
	 *
	 * Each post is assigned the category and tags it belongs to. When those
	 * terms were not imported, they are still used if they already exist,
	 * otherwise the post is created without them. Its featured image is set
	 * when the matching image was imported.
	 *
	 * @param array<string, int> $categoryMap A map of category name to term ID.
	 * @param array<string, int> $tagMap      A map of tag name to term ID.
	 * @param array<string, int> $imageMap    A map of image file name to attachment ID.
	 * @throws \RuntimeException When a post cannot be created.
	 */
	private function importPosts( array $categoryMap, array $tagMap, array $imageMap ): void {
		foreach ( $this->content->posts() as $post ) {
			$postarr = array(
				'post_title'   => $post['title'],
				'post_content' => $post['content'],
				'post_excerpt' => $post['excerpt'],
				'post_type'    => 'post',
				'post_status'  => 'publish',
				'post_author'  => get_current_user_id(),
			);

			$categoryId = $this->resolveTermId( $post['category'], $categoryMap, 'category' );
			if ( $categoryId > 0 ) {
				$postarr['post_category'] = array( $categoryId );
			}

			$postId = wp_insert_post( $postarr, true );

			if ( is_wp_error( $postId ) ) {
				throw new \RuntimeException( esc_html( $postId->get_error_message() ) );
			}

			$tagIds = $this->resolveTermIds( $post['tags'], $tagMap, 'post_tag' );
			if ( array() !== $tagIds ) {
				wp_set_post_terms( $postId, $tagIds, 'post_tag' );
			}

			if ( ! empty( $imageMap[ $post['image'] ] ) ) {
				set_post_thumbnail( $postId, $imageMap[ $post['image'] ] );
			}
		}
	}

	/**
	 * Resolve several term names to term IDs, dropping any that do not resolve.
	 *
	 * @param string[]           $names    The term names.
	 * @param array<string, int> $map      A map of term name to term ID.
	 * @param string             $taxonomy The taxonomy the terms belong to.
	 * @return int[] The resolved term IDs.
	 */
	private function resolveTermIds( array $names, array $map, string $taxonomy ): array {
		$ids = array();

		foreach ( $names as $name ) {
			$id = $this->resolveTermId( $name, $map, $taxonomy );
			if ( $id > 0 ) {
				$ids[] = $id;
			}
		}

		return $ids;
	}

	/**
	 * Resolve a term name to a term ID.
	 *
	 * Prefers the imported term map and falls back to an existing term of the
	 * same name, so posts are still linked to terms that already exist even
	 * when that term type was not imported in this run.
	 *
	 * @param string             $name     The term name.
	 * @param array<string, int> $map      A map of term name to term ID.
	 * @param string             $taxonomy The taxonomy the term belongs to.
	 * @return int The term ID, or 0 when the term does not exist.
	 */
	private function resolveTermId( string $name, array $map, string $taxonomy ): int {
		if ( isset( $map[ $name ] ) ) {
			return $map[ $name ];
		}

		$existing = term_exists( $name, $taxonomy );

		return is_array( $existing ) ? (int) $existing['term_id'] : 0;
	}

	/**
	 * Create the demo pages and configure the front page.
	 *
	 * @throws \RuntimeException When a page cannot be created.
	 */
	private function importPages(): void {
		$home = $this->insertPage( __( 'Home', 'amalfi' ), $this->content->homePage(), 'page-no-title' );
		$this->insertPage( __( 'About', 'amalfi' ), $this->content->aboutPage(), 'page-no-title' );
		$blog = $this->insertPage( __( 'Blog', 'amalfi' ), '' );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home );
		update_option( 'page_for_posts', $blog );
	}

	/**
	 * Insert a single page.
	 *
	 * @param string $title    The page title.
	 * @param string $content  The page block markup.
	 * @param string $template Optional page template slug.
	 * @return int The created page ID.
	 * @throws \RuntimeException When the page cannot be created.
	 */
	private function insertPage( string $title, string $content, string $template = '' ): int {
		$postarr = array(
			'post_title'   => $title,
			'post_content' => $content,
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
		);

		if ( '' !== $template ) {
			$postarr['page_template'] = $template . '.html';
		}

		$pageId = wp_insert_post( $postarr, true );

		if ( is_wp_error( $pageId ) ) {
			throw new \RuntimeException( esc_html( $pageId->get_error_message() ) );
		}

		return (int) $pageId;
	}

	/**
	 * Load the WordPress media sideload helpers on demand.
	 */
	private function ensureMediaFunctions(): void {
		if ( ! function_exists( 'media_handle_sideload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
		}
	}
}
