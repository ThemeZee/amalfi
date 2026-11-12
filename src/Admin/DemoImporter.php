<?php
/**
 * Demo content importer.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Admin;

use Throwable;

/**
 * Renders the demo content setting and imports the selected content.
 */
class DemoImporter {

	/**
	 * Nonce action / field name for the import form.
	 */
	private const NONCE = 'amalfi_demo_content_nonce';

	/**
	 * Submit button name that triggers the import.
	 */
	private const SUBMIT = 'amalfi_import_demo_content';

	/**
	 * Constructor.
	 *
	 * @param ThemeConfig        $config   The theme configuration.
	 * @param SettingsRepository $settings The settings repository.
	 * @param DemoContent        $content  The demo content provider.
	 */
	public function __construct(
		private readonly ThemeConfig $config,
		private readonly SettingsRepository $settings,
		private readonly DemoContent $content,
	) {
	}

	/**
	 * Register the demo content settings field.
	 */
	public function registerFields(): void {
		add_settings_field(
			$this->config->optionName() . '[amalfi_demo_content]',
			esc_html__( 'Import Content', 'amalfi' ),
			array( $this, 'renderField' ),
			$this->config->settingsGroup(),
			AdminPage::SECTION_DEMO
		);
	}

	/**
	 * Render the demo content checkboxes and import button.
	 */
	public function renderField(): void {
		$settings = $this->settings->all();
		$selected = isset( $settings['amalfi_demo_content'] ) && is_array( $settings['amalfi_demo_content'] )
			? $settings['amalfi_demo_content']
			: array();

		foreach ( $this->options() as $key => $label ) {
			$name    = $this->config->optionName() . '[amalfi_demo_content][' . $key . ']';
			$checked = ! empty( $selected[ $key ] );

			printf(
				'<input type="hidden" name="%1$s" value="0" />' .
				'<input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s /> ' .
				'<label for="%1$s">%3$s</label><br/>',
				esc_attr( $name ),
				checked( true, $checked, false ),
				esc_html( $label )
			);
		}

		printf(
			'<br/><input type="submit" class="button" name="%1$s" value="%2$s" />',
			esc_attr( self::SUBMIT ),
			esc_attr__( 'Import Content', 'amalfi' )
		);

		wp_nonce_field( self::NONCE, self::NONCE );
	}

	/**
	 * Handle the import request.
	 */
	public function maybeImport(): void {
		if ( ! isset( $_POST[ self::SUBMIT ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}

		if ( ! current_user_can( $this->config->capability() ) ) {
			return;
		}

		if ( ! check_admin_referer( self::NONCE, self::NONCE ) ) {
			return;
		}

		$selected = $this->selectedParts();

		$this->ensureMediaFunctions();

		$categoryMap = array();
		if ( ! empty( $selected['categories'] ) ) {
			$categoryMap = $this->runStep(
				fn () => $this->importCategories(),
				'demo_import_categories',
				esc_html__( 'Categories were successfully imported.', 'amalfi' ),
				esc_html__( 'Categories could not be imported.', 'amalfi' ),
				array()
			);
		}

		$tagMap = array();
		if ( ! empty( $selected['tags'] ) ) {
			$tagMap = $this->runStep(
				fn () => $this->importTags(),
				'demo_import_tags',
				esc_html__( 'Tags were successfully imported.', 'amalfi' ),
				esc_html__( 'Tags could not be imported.', 'amalfi' ),
				array()
			);
		}

		$imageMap = array();
		if ( ! empty( $selected['image'] ) ) {
			$imageMap = $this->runStep(
				fn () => $this->importFeaturedImages(),
				'demo_import_image',
				esc_html__( 'Featured images were successfully imported.', 'amalfi' ),
				esc_html__( 'Featured images could not be imported.', 'amalfi' ),
				array()
			);
		}

		if ( ! empty( $selected['posts'] ) ) {
			$this->runStep(
				function () use ( $categoryMap, $tagMap, $imageMap ): bool {
					$this->importPosts( $categoryMap, $tagMap, $imageMap );
					return true;
				},
				'demo_import_posts',
				esc_html__( 'Blog posts were successfully imported.', 'amalfi' ),
				esc_html__( 'Blog posts could not be imported.', 'amalfi' ),
				false
			);
		}

		if ( ! empty( $selected['pages'] ) ) {
			$this->runStep(
				function (): bool {
					$this->importPages();
					return true;
				},
				'demo_import_pages',
				esc_html__( 'Static pages were successfully imported.', 'amalfi' ),
				esc_html__( 'Static pages could not be imported.', 'amalfi' ),
				false
			);
		}

		$this->redirectBack();
	}

	/**
	 * The content parts that can be imported, labelled with their amount.
	 *
	 * @return array<string, string>
	 */
	private function options(): array {
		return array(
			/* translators: %d: number of categories to import. */
			'categories' => sprintf( esc_html__( 'Categories (%d)', 'amalfi' ), count( $this->content->categoryNames() ) ),
			/* translators: %d: number of tags to import. */
			'tags'       => sprintf( esc_html__( 'Tags (%d)', 'amalfi' ), count( $this->content->tagNames() ) ),
			/* translators: %d: number of featured images to import. */
			'image'      => sprintf( esc_html__( 'Featured Images (%d)', 'amalfi' ), count( $this->content->imageFiles() ) ),
			/* translators: %d: number of blog posts to import. */
			'posts'      => sprintf( esc_html__( 'Blog Posts (%d)', 'amalfi' ), count( $this->content->postDefinitions() ) ),
			/* translators: %d: number of static pages to import. */
			'pages'      => sprintf( esc_html__( 'Static Pages (%d)', 'amalfi' ), 3 ),
		);
	}

	/**
	 * Return the parts the user selected for import.
	 *
	 * @return array<string, bool>
	 */
	private function selectedParts(): array {
		// Nonce verified in maybeImport() before this runs.
		$raw = isset( $_POST[ $this->config->optionName() ]['amalfi_demo_content'] ) && is_array( $_POST[ $this->config->optionName() ]['amalfi_demo_content'] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? wp_unslash( $_POST[ $this->config->optionName() ]['amalfi_demo_content'] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			: array();

		$selected = array();
		foreach ( array( 'categories', 'tags', 'image', 'posts', 'pages' ) as $key ) {
			$selected[ $key ] = ! empty( $raw[ $key ] );
		}

		return $selected;
	}

	/**
	 * Run a single import step, recording a success or error notice.
	 *
	 * @template T
	 * @param callable $step     The step to run; returns its result.
	 * @param string   $code     Notice code.
	 * @param string   $success  Success message.
	 * @param string   $failure  Failure message.
	 * @param mixed    $fallback Value returned when the step fails.
	 * @return mixed The step result, or the fallback on failure.
	 */
	private function runStep( callable $step, string $code, string $success, string $failure, mixed $fallback ): mixed {
		try {
			$result = $step();
			add_settings_error( $this->config->noticeSlug(), $code, $success, 'success' );

			return $result;
		} catch ( Throwable ) {
			add_settings_error( $this->config->noticeSlug(), $code, $failure, 'error' );

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

	/**
	 * Redirect back to the settings page after an import.
	 */
	private function redirectBack(): void {
		set_transient( 'settings_errors', get_settings_errors(), 30 );
		wp_safe_redirect( $this->config->pageUrl() );
		exit;
	}
}
