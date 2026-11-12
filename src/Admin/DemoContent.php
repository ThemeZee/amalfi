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
	 * The generic demo categories to create.
	 *
	 * @return string[]
	 */
	public function categoryNames(): array {
		return array(
			__( 'Features', 'amalfi' ),
			__( 'Guides', 'amalfi' ),
			__( 'Interviews', 'amalfi' ),
		);
	}

	/**
	 * The generic demo post titles.
	 *
	 * @return string[]
	 */
	public function postTitles(): array {
		return array(
			__( 'A slow morning and a long table', 'amalfi' ),
			__( 'Notes from a week without a plan', 'amalfi' ),
			__( 'The small museum everyone walks past', 'amalfi' ),
			__( 'What the market taught us about dinner', 'amalfi' ),
			__( 'Five short walks worth the detour', 'amalfi' ),
		);
	}

	/**
	 * Absolute path to the bundled featured image.
	 */
	public function featuredImagePath(): string {
		return $this->config->basePath() . '/assets/images/positano-coast.jpg';
	}

	/**
	 * The block markup for a single demo post.
	 */
	public function post(): string {
		return $this->paragraph( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean commodo ligula eget dolor. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.' )
			. $this->paragraph( 'Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu.' )
			. $this->paragraph( 'Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus.' )
			. $this->paragraph( 'Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet.' );
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

	/**
	 * Wrap text in a paragraph block.
	 *
	 * @param string $text The paragraph text.
	 */
	private function paragraph( string $text ): string {
		return "<!-- wp:paragraph -->\n<p>" . $text . "</p>\n<!-- /wp:paragraph -->\n\n";
	}
}
