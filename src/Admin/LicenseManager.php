<?php
/**
 * Theme license management.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Admin;

/**
 * Renders the license fields and handles activation / deactivation against the
 * ThemeZee store (Easy Digital Downloads Software Licensing).
 */
class LicenseManager {

	/**
	 * Nonce action / field name for the license form.
	 */
	private const NONCE = 'amalfi_license_nonce';

	/**
	 * Submit button name that activates the license.
	 */
	private const ACTIVATE = 'amalfi_activate_license';

	/**
	 * Submit button name that deactivates the license.
	 */
	private const DEACTIVATE = 'amalfi_deactivate_license';

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
	 * Register the license settings fields.
	 */
	public function registerFields(): void {
		add_settings_field(
			$this->config->optionName() . '[amalfi_license_status]',
			esc_html__( 'License Status', 'amalfi' ),
			array( $this, 'renderStatusField' ),
			$this->config->settingsGroup(),
			AdminPage::SECTION_LICENSE
		);

		add_settings_field(
			$this->config->optionName() . '[amalfi_license_key]',
			esc_html__( 'License Key', 'amalfi' ),
			array( $this, 'renderKeyField' ),
			$this->config->settingsGroup(),
			AdminPage::SECTION_LICENSE
		);
	}

	/**
	 * Render the license status indicator.
	 */
	public function renderStatusField(): void {
		if ( $this->settings->hasValidLicense() ) {
			printf(
				'<span style="display:inline-block;margin-bottom:12px;padding:4px 8px;background:#008a20;color:#fff;">%s</span><br/>' .
				'<span class="description">%s</span>',
				esc_html__( 'Active', 'amalfi' ),
				esc_html__( 'You are receiving updates.', 'amalfi' )
			);

			return;
		}

		printf(
			'<span style="display:inline-block;margin-bottom:12px;padding:4px 8px;background:#444;color:#fff;">%s</span><br/>' .
			'<span class="description">%s</span>',
			esc_html__( 'Inactive', 'amalfi' ),
			esc_html__( 'Please activate your license.', 'amalfi' )
		);
	}

	/**
	 * Render the license key field and its activate / deactivate button.
	 */
	public function renderKeyField(): void {
		$key = $this->settings->licenseKey();

		if ( $this->settings->hasValidLicense() ) {
			printf(
				'<input type="text" class="regular-text" value="%1$s" readonly="readonly" /> ' .
				'<input type="submit" class="button" name="%2$s" value="%3$s" />',
				esc_attr( $this->maskKey( $key ) ),
				esc_attr( self::DEACTIVATE ),
				esc_attr__( 'Deactivate License', 'amalfi' )
			);
		} else {
			printf(
				'<input type="text" class="regular-text" id="%1$s" name="%1$s" value="%2$s" /> ' .
				'<input type="submit" class="button" name="%3$s" value="%4$s" />',
				esc_attr( $this->config->optionName() . '[amalfi_license_key]' ),
				esc_attr( $key ),
				esc_attr( self::ACTIVATE ),
				esc_attr__( 'Activate License', 'amalfi' )
			);
		}

		wp_nonce_field( self::NONCE, self::NONCE );
	}

	/**
	 * Handle a license activation request.
	 */
	public function maybeActivate(): void {
		if ( ! $this->isRequest( self::ACTIVATE ) ) {
			return;
		}

		// Nonce verified in isRequest() above via check_admin_referer().
		$key = isset( $_POST[ $this->config->optionName() ]['amalfi_license_key'] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			? sanitize_text_field( wp_unslash( $_POST[ $this->config->optionName() ]['amalfi_license_key'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			: '';

		if ( '' === $key ) {
			$this->addError( 'license_empty', esc_html__( 'Please enter a license key.', 'amalfi' ) );
			$this->redirectBack();
		}

		$data = $this->request( 'activate_license', $key );

		if ( null === $data ) {
			$this->redirectBack();
		}

		if ( empty( $data->success ) ) {
			$this->addError( 'license_error', $this->errorMessage( $data ) );
			$this->redirectBack();
		}

		if ( SettingsRepository::STATUS_VALID === ( $data->license ?? '' ) ) {
			$this->settings->storeLicense( $key, SettingsRepository::STATUS_VALID );
			$this->addError( 'license_activated', esc_html__( 'License was successfully activated.', 'amalfi' ), 'success' );
		} else {
			$this->settings->storeLicense( $key, (string) ( $data->license ?? SettingsRepository::STATUS_INACTIVE ) );
		}

		$this->redirectBack();
	}

	/**
	 * Handle a license deactivation request.
	 */
	public function maybeDeactivate(): void {
		if ( ! $this->isRequest( self::DEACTIVATE ) ) {
			return;
		}

		$key = $this->settings->licenseKey();

		if ( '' !== $key ) {
			$this->request( 'deactivate_license', $key );
		}

		$this->settings->clearLicense();
		$this->addError( 'license_deactivated', esc_html__( 'License was deactivated.', 'amalfi' ), 'success' );

		$this->redirectBack();
	}

	/**
	 * Show a reminder notice when the license is not active.
	 */
	public function renderReminder(): void {
		global $pagenow;

		if ( $this->settings->hasValidLicense() ) {
			return;
		}

		if ( ! current_user_can( $this->config->capability() ) ) {
			return;
		}

		$screens = array( 'index.php', 'update-core.php', 'themes.php' );
		if ( ! in_array( $pagenow, $screens, true ) || isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		printf(
			'<div class="notice notice-info"><p>%s</p></div>',
			wp_kses_post(
				sprintf(
					/* translators: %1$s: Theme name. %2$s: URL of the theme settings page. */
					__( 'Please enter your license key for the %1$s theme in order to receive updates and support. <a href="%2$s">Enter License Key</a>', 'amalfi' ),
					esc_html( $this->config->name() ),
					esc_url( $this->config->pageUrl() )
				)
			)
		);
	}

	/**
	 * Remove this theme from wp.org update checks so updates come from the store.
	 *
	 * @param array<string, mixed> $args The HTTP request arguments.
	 * @param string               $url  The request URL.
	 * @return array<string, mixed>
	 */
	public function disableWporgRequest( array $args, string $url ): array {
		if ( ! str_starts_with( $url, 'https://api.wordpress.org/themes/update-check/1.1/' ) ) {
			return $args;
		}

		if ( empty( $args['body']['themes'] ) ) {
			return $args;
		}

		$themes = json_decode( $args['body']['themes'] );

		if ( ! isset( $themes->themes ) ) {
			return $args;
		}

		$parent = get_option( 'template' );
		$child  = get_option( 'stylesheet' );
		unset( $themes->themes->$parent, $themes->themes->$child );

		$args['body']['themes'] = wp_json_encode( $themes );

		return $args;
	}

	/**
	 * Whether the current request is the given license submit, with a valid nonce.
	 *
	 * @param string $submit The submit button name to check.
	 */
	private function isRequest( string $submit ): bool {
		if ( ! isset( $_POST[ $submit ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return false;
		}

		if ( ! current_user_can( $this->config->capability() ) ) {
			return false;
		}

		return (bool) check_admin_referer( self::NONCE, self::NONCE );
	}

	/**
	 * Send a licensing request to the store API.
	 *
	 * @param string $action The EDD license action (activate_license / deactivate_license).
	 * @param string $key    The license key.
	 * @return object|null The decoded response, or null on transport error.
	 */
	private function request( string $action, string $key ): ?object {
		$response = wp_remote_post(
			$this->config->storeUrl(),
			array(
				'timeout'   => 15,
				'sslverify' => true,
				'body'      => array(
					'edd_action'  => $action,
					'license'     => $key,
					'item_id'     => $this->config->itemId(),
					'item_name'   => rawurlencode( $this->config->name() ),
					'url'         => home_url(),
					'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			$this->addError( 'license_api_error', $response->get_error_message() );

			return null;
		}

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$this->addError( 'license_api_error', esc_html__( 'An error occurred, please try again.', 'amalfi' ) );

			return null;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ) );

		return is_object( $data ) ? $data : null;
	}

	/**
	 * Translate an EDD error response into a human readable message.
	 *
	 * @param object $data The decoded API response.
	 */
	private function errorMessage( object $data ): string {
		$error = $data->error ?? '';

		switch ( $error ) {
			case 'expired':
				return sprintf(
					/* translators: %s: license expiration date. */
					esc_html__( 'Your license key expired on %s.', 'amalfi' ),
					isset( $data->expires )
						? esc_html( date_i18n( get_option( 'date_format' ), strtotime( $data->expires, time() ) ) )
						: ''
				);

			case 'disabled':
			case 'revoked':
				return esc_html__( 'Your license key has been disabled.', 'amalfi' );

			case 'missing':
				return esc_html__( 'Invalid license key.', 'amalfi' );

			case 'invalid':
			case 'site_inactive':
				return esc_html__( 'Your license is not active for this URL.', 'amalfi' );

			case 'item_name_mismatch':
				return sprintf(
					/* translators: %s: theme name. */
					esc_html__( 'This appears to be an invalid license key for %s.', 'amalfi' ),
					esc_html( $this->config->name() )
				);

			case 'no_activations_left':
				return esc_html__( 'Your license key has reached its activation limit.', 'amalfi' );

			default:
				return esc_html__( 'An error occurred, please try again.', 'amalfi' );
		}
	}

	/**
	 * Mask all but the final characters of a license key.
	 *
	 * @param string $key The license key.
	 */
	private function maskKey( string $key ): string {
		$visible = substr( $key, -5 );

		return str_repeat( '*', max( 0, strlen( $key ) - 5 ) ) . $visible;
	}

	/**
	 * Record a settings notice.
	 *
	 * @param string $code    The notice code.
	 * @param string $message The notice message.
	 * @param string $type    The notice type (error / success).
	 */
	private function addError( string $code, string $message, string $type = 'error' ): void {
		add_settings_error( $this->config->noticeSlug(), $code, $message, $type );
	}

	/**
	 * Redirect back to the settings page, preserving notices.
	 */
	private function redirectBack(): void {
		set_transient( 'settings_errors', get_settings_errors(), 30 );
		wp_safe_redirect( $this->config->pageUrl() );
		exit;
	}
}
