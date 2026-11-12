<?php
/**
 * Theme setup admin page.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Admin;

/**
 * Registers the "Theme Setup" page under the Appearance menu.
 *
 * This service owns the page itself and its settings sections. The individual
 * settings fields are registered by the feature services ({@see DemoImporter}
 * and {@see LicenseManager}) into the sections declared here.
 */
class AdminPage {

	/**
	 * Identifier of the demo content section.
	 */
	public const SECTION_DEMO = 'amalfi_demo_section';

	/**
	 * Identifier of the license section.
	 */
	public const SECTION_LICENSE = 'amalfi_license_section';

	/**
	 * Constructor.
	 *
	 * @param ThemeConfig        $config   The theme configuration.
	 * @param SettingsRepository $settings The settings repository.
	 */
	public function __construct(
		private readonly ThemeConfig $config,
		private readonly SettingsRepository $settings,
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
	 * Register the settings container and its sections.
	 */
	public function registerSettings(): void {
		$this->settings->ensureExists();

		add_settings_section(
			self::SECTION_DEMO,
			esc_html__( 'Demo Content', 'amalfi' ),
			array( $this, 'renderDemoIntro' ),
			$this->config->settingsGroup()
		);

		add_settings_section(
			self::SECTION_LICENSE,
			esc_html__( 'License Settings', 'amalfi' ),
			array( $this, 'renderLicenseIntro' ),
			$this->config->settingsGroup()
		);

		register_setting(
			$this->config->settingsGroup(),
			$this->config->optionName(),
			array( $this, 'sanitize' )
		);
	}

	/**
	 * Render the settings page.
	 */
	public function render(): void {
		?>
		<div class="wrap">
			<h1>
				<?php
				printf(
					/* translators: %1$s: Theme name. %2$s: Theme version. */
					esc_html__( '%1$s %2$s', 'amalfi' ),
					esc_html( $this->config->name() ),
					esc_html( $this->config->version() )
				);
				?>
			</h1>

			<form method="post" action="options.php">
				<?php
				do_settings_sections( $this->config->settingsGroup() );
				settings_fields( $this->config->settingsGroup() );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the admin notices for this page.
	 */
	public function renderNotices(): void {
		settings_errors( $this->config->noticeSlug() );
	}

	/**
	 * Render the demo section introduction.
	 */
	public function renderDemoIntro(): void {
		esc_html_e( 'Import the demo content into your website to get started quickly.', 'amalfi' );
	}

	/**
	 * Render the license section introduction.
	 */
	public function renderLicenseIntro(): void {
		esc_html_e( 'An active license key is needed to install updates and receive support.', 'amalfi' );
	}

	/**
	 * Sanitize the submitted settings.
	 *
	 * @param mixed $input The submitted settings.
	 * @return array<string, mixed>
	 */
	public function sanitize( $input = array() ): array {
		$input = is_array( $input ) ? $input : array();

		// Only the Settings API "Save Changes" submit carries a referer; the
		// custom import/license buttons handle their own persistence, so leave
		// the stored values untouched for those requests.
		if ( empty( $_POST['_wp_http_referer'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return $input;
		}

		$stored = get_option( $this->config->optionName(), array() );
		$stored = is_array( $stored ) ? $stored : array();

		foreach ( $input as $key => $value ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $checkbox => $enabled ) {
					$input[ $key ][ $checkbox ] = ! empty( $enabled ) ? 1 : 0;
				}
				continue;
			}

			$input[ $key ] = sanitize_text_field( (string) $value );
		}

		return array_merge( $stored, $input );
	}
}
