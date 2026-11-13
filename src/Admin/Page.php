<?php
/**
 * Theme Setup page.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Admin;

use ThemeZee\Amalfi\Core\ThemeConfig;

/**
 * Registers the "Theme Setup" page under the Appearance menu and renders its
 * server-side chrome.
 *
 * The header, theme links, "Getting started" heading and documentation section
 * are rendered with PHP so they paint instantly. Only the Demo Content and
 * License Settings controls are hydrated client-side, into the mount element
 * this page outputs (see {@see Assets} for the enqueue and bootstrap data).
 */
class Page {

	/**
	 * The DOM id the React app mounts on (the demo/license controls only).
	 */
	private const MOUNT_ID = 'amalfi-demo-license';

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
	 * Register the settings page under Appearance.
	 */
	public function registerPage(): void {
		add_theme_page(
			esc_html__( 'Theme Setup', 'amalfi' ),
			esc_html__( 'Theme Setup', 'amalfi' ),
			$this->config->capability(),
			$this->config->pageSlug(),
			array( $this, 'render' )
		);
	}

	/**
	 * Render the page. The static chrome is server-rendered; the React app
	 * hydrates only the Demo Content / License Settings container.
	 */
	public function render(): void {
		$name    = $this->config->name();
		$version = $this->config->version();
		?>
		<div class="wrap">
			<div class="amalfi-theme-info">

				<h1 class="amalfi-theme-info__title">
					<?php
					/* translators: %1$s: theme name. %2$s: version. */
					printf( esc_html__( 'Welcome to %1$s %2$s', 'amalfi' ), esc_html( $name ), esc_html( $version ) );
					?>
				</h1>

				<?php if ( '' !== $this->config->description() ) : ?>
					<p class="amalfi-theme-info__description">
						<?php echo esc_html( $this->config->description() ); ?>
					</p>
				<?php endif; ?>

				<hr />

				<p class="amalfi-theme-info__links">
					<strong><?php esc_html_e( 'Theme Links:', 'amalfi' ); ?></strong>
					<?php foreach ( $this->links() as $link ) : ?>
						<a href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noreferrer noopener">
							<?php echo esc_html( $link['label'] ); ?>
						</a>
					<?php endforeach; ?>
				</p>

				<hr />

				<section class="amalfi-theme-info__getting-started">
					<h2>
						<?php
						/* translators: %s: theme name. */
						printf( esc_html__( 'Getting started with %s', 'amalfi' ), esc_html( $name ) );
						?>
					</h2>

					<div id="<?php echo esc_attr( self::MOUNT_ID ); ?>" class="amalfi-theme-setup__app">
						<?php esc_html_e( 'Loading…', 'amalfi' ); ?>
					</div>
				</section>

				<hr />

				<section class="amalfi-theme-info__documentation">
					<h2><?php esc_html_e( 'Theme Documentation', 'amalfi' ); ?></h2>
					<p class="amalfi-theme-info__about">
						<?php esc_html_e( 'Need help to set up and configure this theme? We have you covered. Check out the extensive theme documentation on our website.', 'amalfi' ); ?>
					</p>
					<a class="button button-secondary" href="<?php echo esc_url( $this->docsUrl() ); ?>" target="_blank" rel="noreferrer noopener">
						<?php
						/* translators: %s: theme name. */
						printf( esc_html__( "View %s's documentation", 'amalfi' ), esc_html( $name ) );
						?>
					</a>
				</section>

			</div>
		</div>
		<?php
	}

	/**
	 * The theme links shown in the header.
	 *
	 * @return array<int, array{label: string, url: string}>
	 */
	private function links(): array {
		$slug  = $this->config->slug();
		$store = $this->config->storeUrl();
		$home  = '' !== $this->config->uri() ? $this->config->uri() : $store . '/themes/' . $slug . '/';

		return array(
			array(
				'label' => __( 'Theme Page', 'amalfi' ),
				'url'   => $home,
			),
			array(
				'label' => __( 'Theme Demo', 'amalfi' ),
				'url'   => 'https://preview.themezee.com/?demo=' . $slug,
			),
			array(
				'label' => __( 'Theme Documentation', 'amalfi' ),
				'url'   => $this->docsUrl(),
			),
			array(
				'label' => __( 'Theme Changelog', 'amalfi' ),
				'url'   => $store . '/changelogs/?action=themezee-changelog&type=theme&slug=' . $slug,
			),
		);
	}

	/**
	 * The theme documentation URL.
	 */
	private function docsUrl(): string {
		return $this->config->storeUrl() . '/docs/' . $this->config->slug() . '-documentation/';
	}
}
