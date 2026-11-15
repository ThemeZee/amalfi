<?php
/**
 * Demo content markup provider.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\DemoContent;

use ThemeZee\Amalfi\Core\ThemeConfig;

/**
 * Provides the block markup for the demo pages and posts.
 *
 * Both pages and posts are stored as bundled block-markup files under
 * `demo-content/` — posts in `demo-content/posts/`, pages in
 * `demo-content/pages/` — and loaded on demand. The markup is written with
 * generic, distribution-safe copy. Internal links use the `%HOME_URL%`
 * placeholder token, which is resolved to the site's home URL on import (see
 * {@see self::placeholders()}), so the content works on any install — including
 * a sub-directory or multisite sub-site — rather than being tied to this demo.
 *
 * Demo files are read as data only: they are never included or evaluated, and
 * token replacement is a fixed, one-pass string substitution, so the bundled
 * markup cannot execute code or alter the replacement values.
 */
class Content {

	/**
	 * Constructor.
	 *
	 * @param ThemeConfig $config The theme configuration.
	 */
	public function __construct(
		private readonly ThemeConfig $config,
	) {
	}

	/**
	 * The demo categories to create.
	 *
	 * @return string[]
	 */
	public function categoryNames(): array {
		return array(
			__( 'Destinations', 'amalfi' ),
			__( 'Food & Drink', 'amalfi' ),
			__( 'Things to Do', 'amalfi' ),
			__( 'Travel Tips', 'amalfi' ),
			__( 'Where to Stay', 'amalfi' ),
		);
	}

	/**
	 * The demo post tags to create.
	 *
	 * These are the distinct tag names referenced by the demo posts (see the
	 * `tags` key of each entry in {@see self::postDefinitions()}).
	 *
	 * @return string[]
	 */
	public function tagNames(): array {
		return array(
			'amalfi-town',
			'beaches',
			'boat-trips',
			'budget',
			'capri',
			'ferry',
			'hiking',
			'limoncello',
			'luxury',
			'naples',
			'pompeii',
			'positano',
			'ravello',
			'romantic',
			'seafood',
			'sorrento',
		);
	}

	/**
	 * The demo blog posts to create.
	 *
	 * Each entry describes a post: its title, the category it belongs to, a
	 * short excerpt, the base name of the block markup file under
	 * `demo-content/posts/`, the bundled featured image file name, and the tag
	 * names it is assigned. The content itself is loaded on demand by
	 * {@see self::posts()} so the large markup stays out of this class.
	 *
	 * @return array<int, array{title: string, category: string, excerpt: string, file: string, image: string, tags: string[]}>
	 */
	public function postDefinitions(): array {
		return array(
			array(
				'title'    => __( "A First-Timer's Guide to the Amalfi Coast", 'amalfi' ),
				'category' => __( 'Travel Tips', 'amalfi' ),
				'excerpt'  => __( "Everything we wish we'd known before our first trip — when to go, where to base yourself, and how to arrive.", 'amalfi' ),
				'file'     => 'amalfi-coast-first-timers-guide',
				'image'    => 'amalfi-coast-panorama.jpg',
				'tags'     => array( 'amalfi-town', 'budget', 'ferry' ),
			),
			array(
				'title'    => __( 'Positano: The Vertical Village That Steals Every Heart', 'amalfi' ),
				'category' => __( 'Destinations', 'amalfi' ),
				'excerpt'  => __( "Approach by sea and Positano rises like an amphitheatre of colour. A love letter to the coast's most famous village.", 'amalfi' ),
				'file'     => 'positano-vertical-village',
				'image'    => 'positano-coast.jpg',
				'tags'     => array( 'beaches', 'positano', 'romantic' ),
			),
			array(
				'title'    => __( 'Walking the Path of the Gods', 'amalfi' ),
				'category' => __( 'Things to Do', 'amalfi' ),
				'excerpt'  => __( 'The Sentiero degli Dei is the coast\'s greatest walk — three hours of ridgeline views from Bomerano to Nocelle.', 'amalfi' ),
				'file'     => 'walking-path-of-the-gods',
				'image'    => 'amalfi-cliff-view.jpg',
				'tags'     => array( 'hiking', 'positano' ),
			),
			array(
				'title'    => __( 'A Day Trip to Capri from the Amalfi Coast', 'amalfi' ),
				'category' => __( 'Destinations', 'amalfi' ),
				'excerpt'  => __( 'Faraglioni rocks, a chic piazzetta, and a glowing blue grotto. How to make the most of a day on Capri.', 'amalfi' ),
				'file'     => 'day-trip-to-capri',
				'image'    => 'capri-faraglioni.jpg',
				'tags'     => array( 'boat-trips', 'capri', 'ferry' ),
			),
			array(
				'title'    => __( 'Where to Stay: Cute Hotels & Hidden B&Bs', 'amalfi' ),
				'category' => __( 'Where to Stay', 'amalfi' ),
				'excerpt'  => __( 'From cliff-edge grand hotels to lemon-grove B&Bs — how to choose where to sleep on the Amalfi Coast.', 'amalfi' ),
				'file'     => 'where-to-stay-amalfi-coast',
				'image'    => 'amalfi-ravello-port.jpg',
				'tags'     => array( 'budget', 'luxury', 'romantic' ),
			),
			array(
				'title'    => __( 'Pompeii & Vesuvius: A Journey into Ancient History', 'amalfi' ),
				'category' => __( 'Destinations', 'amalfi' ),
				'excerpt'  => __( 'An easy train ride from Naples, the city frozen by Vesuvius in AD 79 makes the ancient world startlingly real.', 'amalfi' ),
				'file'     => 'pompeii-vesuvius-day-trip',
				'image'    => 'pompeii-vesuvius.jpg',
				'tags'     => array( 'naples', 'pompeii' ),
			),
			array(
				'title'    => __( 'The Best Seafood Spots Along the Coast', 'amalfi' ),
				'category' => __( 'Food & Drink', 'amalfi' ),
				'excerpt'  => __( "From harbour fritto misto to Cetara's anchovies — where and what to eat along the coast.", 'amalfi' ),
				'file'     => 'best-seafood-amalfi-coast',
				'image'    => 'coastal-seafood.jpg',
				'tags'     => array( 'amalfi-town', 'seafood' ),
			),
			array(
				'title'    => __( 'Limoncello: The Taste of the Amalfi Lemons', 'amalfi' ),
				'category' => __( 'Food & Drink', 'amalfi' ),
				'excerpt'  => __( "Bright, cold and shockingly lemony: the story of the Amalfi's giant lemons and the drink that ends every meal.", 'amalfi' ),
				'file'     => 'limoncello-amalfi-lemons',
				'image'    => 'amalfi-lemons.jpg',
				'tags'     => array( 'limoncello', 'sorrento' ),
			),
			array(
				'title'    => __( 'Ravello: Gardens, Music & Mountaintop Views', 'amalfi' ),
				'category' => __( 'Destinations', 'amalfi' ),
				'excerpt'  => __( 'High above the coast, Ravello is a town of gardens, music, and views that stop conversation.', 'amalfi' ),
				'file'     => 'ravello-gardens-and-views',
				'image'    => 'ravello-garden.jpg',
				'tags'     => array( 'ravello', 'romantic' ),
			),
			array(
				'title'    => __( 'Getting Around: Ferries, Buses & the Coastal Road', 'amalfi' ),
				'category' => __( 'Travel Tips', 'amalfi' ),
				'excerpt'  => __( 'Ferries, blue SITA buses, or a private driver? How to move between the villages without the stress.', 'amalfi' ),
				'file'     => 'getting-around-amalfi-coast',
				'image'    => 'amalfi-boat.jpg',
				'tags'     => array( 'boat-trips', 'budget', 'ferry' ),
			),
			array(
				'title'    => __( 'Beach-Hopping on the Amalfi Coast', 'amalfi' ),
				'category' => __( 'Things to Do', 'amalfi' ),
				'excerpt'  => __( 'Small, pebbly coves between the cliffs — the best beaches to find, and the secret ones you reach by boat.', 'amalfi' ),
				'file'     => 'beach-hopping-amalfi-coast',
				'image'    => 'mediterranean-beach.jpg',
				'tags'     => array( 'beaches', 'boat-trips', 'sorrento' ),
			),
			array(
				'title'    => __( '48 Hours in Naples: Pizza, Culture & Chaos', 'amalfi' ),
				'category' => __( 'Destinations', 'amalfi' ),
				'excerpt'  => __( "Don't rush through Italy's most exuberant city. Two days of history, views, and the world's best pizza.", 'amalfi' ),
				'file'     => '48-hours-in-naples',
				'image'    => 'naples-waterfront-sunset.jpg',
				'tags'     => array( 'naples', 'pompeii' ),
			),
		);
	}

	/**
	 * The demo blog posts, each with its content markup loaded.
	 *
	 * @return array<int, array{title: string, category: string, excerpt: string, content: string, image: string, tags: string[]}>
	 */
	public function posts(): array {
		$posts = array();

		foreach ( $this->postDefinitions() as $post ) {
			$posts[] = array(
				'title'    => $post['title'],
				'category' => $post['category'],
				'excerpt'  => $post['excerpt'],
				'content'  => $this->postContent( $post['file'] ),
				'image'    => $post['image'],
				'tags'     => $post['tags'],
			);
		}

		return $posts;
	}

	/**
	 * The unique featured image file names used by the demo posts.
	 *
	 * @return string[]
	 */
	public function imageFiles(): array {
		return array_values( array_unique( array_column( $this->postDefinitions(), 'image' ) ) );
	}

	/**
	 * Absolute path to a bundled demo image.
	 *
	 * @param string $file The image file name.
	 */
	public function imagePath( string $file ): string {
		return $this->config->basePath() . '/assets/images/' . $file;
	}

	/**
	 * Load the block markup for a single demo post.
	 *
	 * @param string $file Post markup file base name (without extension).
	 */
	private function postContent( string $file ): string {
		$path = $this->config->basePath() . '/demo-content/posts/' . $file . '.html';

		if ( ! is_readable( $path ) ) {
			return '';
		}

		// Reading a bundled local theme file, not a remote URL.
		return trim( (string) file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	}

	/**
	 * The demo pages to create.
	 *
	 * Each entry describes a page: its title, the base name of the block markup
	 * file under `demo-content/pages/` (an empty string for a page with no
	 * content, such as the blog posts page), an optional page template slug,
	 * and which role it plays in the front-page configuration. The content
	 * itself is loaded on demand by {@see self::pages()} so the markup stays out
	 * of this class.
	 *
	 * @return array<int, array{title: string, file: string, template: string, front: bool, posts: bool}>
	 */
	public function pageDefinitions(): array {
		return array(
			array(
				'title'    => __( 'Home', 'amalfi' ),
				'file'     => 'home',
				'template' => 'page-no-title',
				'front'    => true,
				'posts'    => false,
			),
			array(
				'title'    => __( 'Activities', 'amalfi' ),
				'file'     => 'activities',
				'template' => 'page-no-title',
				'front'    => false,
				'posts'    => false,
			),
			array(
				'title'    => __( 'Destinations', 'amalfi' ),
				'file'     => 'destinations',
				'template' => '',
				'front'    => false,
				'posts'    => false,
			),
			array(
				'title'    => __( 'About', 'amalfi' ),
				'file'     => 'about',
				'template' => '',
				'front'    => false,
				'posts'    => false,
			),
			array(
				'title'    => __( 'Blog', 'amalfi' ),
				'file'     => '',
				'template' => '',
				'front'    => false,
				'posts'    => true,
			),
			array(
				'title'    => __( 'Contact', 'amalfi' ),
				'file'     => 'contact',
				'template' => '',
				'front'    => false,
				'posts'    => false,
			),
		);
	}

	/**
	 * The demo pages, each with its content markup loaded.
	 *
	 * @return array<int, array{title: string, content: string, template: string, front: bool, posts: bool}>
	 */
	public function pages(): array {
		$pages = array();

		foreach ( $this->pageDefinitions() as $page ) {
			$pages[] = array(
				'title'    => $page['title'],
				'content'  => '' === $page['file'] ? '' : $this->pageContent( $page['file'] ),
				'template' => $page['template'],
				'front'    => $page['front'],
				'posts'    => $page['posts'],
			);
		}

		return $pages;
	}

	/**
	 * Load the block markup for a single demo page.
	 *
	 * The file is read as plain text — never included or evaluated — and its
	 * placeholder tokens are resolved before the markup is returned.
	 *
	 * @param string $file Page markup file base name (without extension).
	 */
	private function pageContent( string $file ): string {
		$path = $this->config->basePath() . '/demo-content/pages/' . $file . '.html';

		if ( ! is_readable( $path ) ) {
			return '';
		}

		// Reading a bundled local theme file, not a remote URL.
		$markup = trim( (string) file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		return $this->resolvePlaceholders( $markup );
	}

	/**
	 * Replace the placeholder tokens in demo markup with their dynamic values.
	 *
	 * This is a plain, one-pass string substitution: only the fixed tokens
	 * defined in {@see self::placeholders()} are replaced, each with a trusted
	 * value produced by WordPress core (never with anything read from the file).
	 * The markup is treated purely as data — it is never executed, interpolated
	 * as PHP, or passed to a callback — so a demo file cannot inject code or
	 * influence what the replacements are. Unknown `%TOKENS%` are left untouched
	 * as literal text.
	 *
	 * @param string $markup The raw demo markup.
	 */
	private function resolvePlaceholders( string $markup ): string {
		return strtr( $markup, $this->placeholders() );
	}

	/**
	 * The placeholder tokens demo files may use, mapped to their values.
	 *
	 * Keys are the literal tokens as they appear in the markup; values are the
	 * strings they resolve to. Every value is derived from WordPress core and
	 * escaped for its use in the markup (URLs with {@see esc_url()}), so the
	 * result is safe to store as post content.
	 *
	 * @return array<string, string> A map of token to replacement value.
	 */
	private function placeholders(): array {
		return array(
			// The site's home URL without a trailing slash, so a file can write
			// `%HOME_URL%/blog/` and have it resolve on any install (including a
			// sub-directory or multisite sub-site).
			'%HOME_URL%' => esc_url( untrailingslashit( home_url() ) ),
		);
	}
}
