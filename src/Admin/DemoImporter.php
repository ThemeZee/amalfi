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

		$categoryIds = array();
		if ( ! empty( $selected['categories'] ) ) {
			$categoryIds = $this->runStep(
				fn () => $this->importCategories(),
				'demo_import_categories',
				esc_html__( 'Categories were successfully imported.', 'amalfi' ),
				esc_html__( 'Categories could not be imported.', 'amalfi' ),
				array()
			);
		}

		$imageId = 0;
		if ( ! empty( $selected['image'] ) ) {
			$imageId = $this->runStep(
				fn () => $this->importFeaturedImage(),
				'demo_import_image',
				esc_html__( 'The featured image was successfully imported.', 'amalfi' ),
				esc_html__( 'The featured image could not be imported.', 'amalfi' ),
				0
			);
		}

		if ( ! empty( $selected['posts'] ) ) {
			$this->runStep(
				function () use ( $categoryIds, $imageId ): bool {
					$this->importPosts( $categoryIds, $imageId );
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
			'image'      => esc_html__( 'Featured Image (1)', 'amalfi' ),
			/* translators: %d: number of blog posts to import. */
			'posts'      => sprintf( esc_html__( 'Blog Posts (%d)', 'amalfi' ), count( $this->content->postTitles() ) ),
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
		foreach ( array( 'categories', 'image', 'posts', 'pages' ) as $key ) {
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
	 * @return int[] The created term IDs.
	 * @throws \RuntimeException When a category cannot be created.
	 */
	private function importCategories(): array {
		$ids = array();

		foreach ( $this->content->categoryNames() as $name ) {
			$term = wp_create_term( $name, 'category' );

			if ( is_wp_error( $term ) ) {
				throw new \RuntimeException( esc_html( $term->get_error_message() ) );
			}

			$ids[] = (int) $term['term_id'];
		}

		return $ids;
	}

	/**
	 * Sideload the bundled featured image into the media library.
	 *
	 * @return int The attachment ID.
	 * @throws \RuntimeException When the image cannot be imported.
	 */
	private function importFeaturedImage(): int {
		$source = $this->content->featuredImagePath();

		if ( ! is_readable( $source ) ) {
			throw new \RuntimeException( 'Demo image not found.' );
		}

		$temp = wp_tempnam( basename( $source ) );
		if ( ! $temp || ! copy( $source, $temp ) ) {
			throw new \RuntimeException( 'Could not copy demo image.' );
		}

		$file = array(
			'name'     => 'amalfi-' . basename( $source ),
			'tmp_name' => $temp,
		);

		$id = media_handle_sideload( $file, 0 );

		if ( is_wp_error( $id ) ) {
			wp_delete_file( $temp );
			throw new \RuntimeException( esc_html( $id->get_error_message() ) );
		}

		return (int) $id;
	}

	/**
	 * Create the demo posts.
	 *
	 * @param int[] $categoryIds The category IDs to assign at random.
	 * @param int   $imageId     The featured image ID, or 0 for none.
	 * @throws \RuntimeException When a post cannot be created.
	 */
	private function importPosts( array $categoryIds, int $imageId ): void {
		foreach ( $this->content->postTitles() as $title ) {
			$postarr = array(
				'post_title'   => $title,
				'post_content' => $this->content->post(),
				'post_type'    => 'post',
				'post_status'  => 'publish',
				'post_author'  => get_current_user_id(),
			);

			if ( array() !== $categoryIds ) {
				$postarr['post_category'] = array( $categoryIds[ array_rand( $categoryIds ) ] );
			}

			$postId = wp_insert_post( $postarr, true );

			if ( is_wp_error( $postId ) ) {
				throw new \RuntimeException( esc_html( $postId->get_error_message() ) );
			}

			if ( $imageId > 0 ) {
				set_post_thumbnail( $postId, $imageId );
			}
		}
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
