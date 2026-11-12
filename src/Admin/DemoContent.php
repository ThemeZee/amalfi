<?php
/**
 * Demo content markup provider.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Admin;

/**
 * Builds the block markup for the demo pages and posts.
 *
 * Pages are composed from the theme's own block patterns, so the demo content
 * always reflects the current patterns and stays distribution-safe. Posts use
 * generic placeholder copy.
 */
class DemoContent {

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
	 * The demo blog posts to create.
	 *
	 * Each entry describes a post: its title, the category it belongs to, a
	 * short excerpt and the base name of the block markup file under
	 * `demo-content/posts/`. The content itself is loaded on demand by
	 * {@see self::posts()} so the large markup stays out of this class.
	 *
	 * @return array<int, array{title: string, category: string, excerpt: string, file: string}>
	 */
	public function postDefinitions(): array {
		return array(
			array(
				'title'    => __( "A First-Timer's Guide to the Amalfi Coast", 'amalfi' ),
				'category' => __( 'Travel Tips', 'amalfi' ),
				'excerpt'  => __( "Everything we wish we'd known before our first trip — when to go, where to base yourself, and how to arrive.", 'amalfi' ),
				'file'     => 'amalfi-coast-first-timers-guide',
			),
			array(
				'title'    => __( 'Positano: The Vertical Village That Steals Every Heart', 'amalfi' ),
				'category' => __( 'Destinations', 'amalfi' ),
				'excerpt'  => __( "Approach by sea and Positano rises like an amphitheatre of colour. A love letter to the coast's most famous village.", 'amalfi' ),
				'file'     => 'positano-vertical-village',
			),
			array(
				'title'    => __( 'Walking the Path of the Gods', 'amalfi' ),
				'category' => __( 'Things to Do', 'amalfi' ),
				'excerpt'  => __( 'The Sentiero degli Dei is the coast\'s greatest walk — three hours of ridgeline views from Bomerano to Nocelle.', 'amalfi' ),
				'file'     => 'walking-path-of-the-gods',
			),
			array(
				'title'    => __( 'A Day Trip to Capri from the Amalfi Coast', 'amalfi' ),
				'category' => __( 'Destinations', 'amalfi' ),
				'excerpt'  => __( 'Faraglioni rocks, a chic piazzetta, and a glowing blue grotto. How to make the most of a day on Capri.', 'amalfi' ),
				'file'     => 'day-trip-to-capri',
			),
			array(
				'title'    => __( 'Where to Stay: Cute Hotels & Hidden B&Bs', 'amalfi' ),
				'category' => __( 'Where to Stay', 'amalfi' ),
				'excerpt'  => __( 'From cliff-edge grand hotels to lemon-grove B&Bs — how to choose where to sleep on the Amalfi Coast.', 'amalfi' ),
				'file'     => 'where-to-stay-amalfi-coast',
			),
			array(
				'title'    => __( 'Pompeii & Vesuvius: A Journey into Ancient History', 'amalfi' ),
				'category' => __( 'Destinations', 'amalfi' ),
				'excerpt'  => __( 'An easy train ride from Naples, the city frozen by Vesuvius in AD 79 makes the ancient world startlingly real.', 'amalfi' ),
				'file'     => 'pompeii-vesuvius-day-trip',
			),
			array(
				'title'    => __( 'The Best Seafood Spots Along the Coast', 'amalfi' ),
				'category' => __( 'Food & Drink', 'amalfi' ),
				'excerpt'  => __( "From harbour fritto misto to Cetara's anchovies — where and what to eat along the coast.", 'amalfi' ),
				'file'     => 'best-seafood-amalfi-coast',
			),
			array(
				'title'    => __( 'Limoncello: The Taste of the Amalfi Lemons', 'amalfi' ),
				'category' => __( 'Food & Drink', 'amalfi' ),
				'excerpt'  => __( "Bright, cold and shockingly lemony: the story of the Amalfi's giant lemons and the drink that ends every meal.", 'amalfi' ),
				'file'     => 'limoncello-amalfi-lemons',
			),
			array(
				'title'    => __( 'Ravello: Gardens, Music & Mountaintop Views', 'amalfi' ),
				'category' => __( 'Destinations', 'amalfi' ),
				'excerpt'  => __( 'High above the coast, Ravello is a town of gardens, music, and views that stop conversation.', 'amalfi' ),
				'file'     => 'ravello-gardens-and-views',
			),
			array(
				'title'    => __( 'Getting Around: Ferries, Buses & the Coastal Road', 'amalfi' ),
				'category' => __( 'Travel Tips', 'amalfi' ),
				'excerpt'  => __( 'Ferries, blue SITA buses, or a private driver? How to move between the villages without the stress.', 'amalfi' ),
				'file'     => 'getting-around-amalfi-coast',
			),
			array(
				'title'    => __( 'Beach-Hopping on the Amalfi Coast', 'amalfi' ),
				'category' => __( 'Things to Do', 'amalfi' ),
				'excerpt'  => __( 'Small, pebbly coves between the cliffs — the best beaches to find, and the secret ones you reach by boat.', 'amalfi' ),
				'file'     => 'beach-hopping-amalfi-coast',
			),
			array(
				'title'    => __( '48 Hours in Naples: Pizza, Culture & Chaos', 'amalfi' ),
				'category' => __( 'Destinations', 'amalfi' ),
				'excerpt'  => __( "Don't rush through Italy's most exuberant city. Two days of history, views, and the world's best pizza.", 'amalfi' ),
				'file'     => '48-hours-in-naples',
			),
		);
	}

	/**
	 * The demo blog posts, each with its content markup loaded.
	 *
	 * @return array<int, array{title: string, category: string, excerpt: string, content: string}>
	 */
	public function posts(): array {
		$posts = array();

		foreach ( $this->postDefinitions() as $post ) {
			$posts[] = array(
				'title'    => $post['title'],
				'category' => $post['category'],
				'excerpt'  => $post['excerpt'],
				'content'  => $this->postContent( $post['file'] ),
			);
		}

		return $posts;
	}

	/**
	 * Absolute path to the bundled featured image.
	 */
	public function featuredImagePath(): string {
		return $this->config->basePath() . '/assets/images/positano-coast.jpg';
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
	 * The block markup for the demo home page.
	 */
	public function homePage(): string {
		return $this->patterns(
			'hero-centered',
			'featured-story',
			'highlights-cards',
			'newsletter-cta'
		);
	}

	/**
	 * The block markup for the demo about page.
	 */
	public function aboutPage(): string {
		return $this->patterns(
			'hero-image',
			'contributors-cards',
			'testimonials-columns'
		);
	}

	/**
	 * Compose markup by rendering the given theme patterns in order.
	 *
	 * @param string ...$slugs Pattern file base names (without extension).
	 */
	private function patterns( string ...$slugs ): string {
		$markup = '';

		foreach ( $slugs as $slug ) {
			$markup .= $this->renderPattern( $slug ) . "\n\n";
		}

		return trim( $markup );
	}

	/**
	 * Render a single pattern file to its block markup.
	 *
	 * @param string $slug Pattern file base name (without extension).
	 */
	private function renderPattern( string $slug ): string {
		$file = $this->config->basePath() . '/patterns/' . $slug . '.php';

		if ( ! is_readable( $file ) ) {
			return '';
		}

		ob_start();
		include $file;

		return trim( (string) ob_get_clean() );
	}
}
