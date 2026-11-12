<?php
/**
 * Theme settings repository.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Admin;

/**
 * Reads and writes the theme settings option.
 *
 * All Admin services go through this repository instead of calling
 * `get_option()` / `update_option()` directly, so the option name and its
 * default shape live in exactly one place.
 */
class SettingsRepository {

	/**
	 * License is active and updates are available.
	 */
	public const STATUS_VALID = 'valid';

	/**
	 * License is not active.
	 */
	public const STATUS_INACTIVE = 'inactive';

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
	 * Return all settings merged over their defaults.
	 *
	 * @return array<string, mixed>
	 */
	public function all(): array {
		$stored = get_option( $this->config->optionName(), array() );

		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		return wp_parse_args( $stored, $this->defaults() );
	}

	/**
	 * Persist the given settings, merging them over the stored values.
	 *
	 * @param array<string, mixed> $settings The settings to store.
	 */
	public function save( array $settings ): void {
		$stored = get_option( $this->config->optionName(), array() );

		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		update_option( $this->config->optionName(), array_merge( $stored, $settings ) );
	}

	/**
	 * Return the stored license key, or an empty string.
	 */
	public function licenseKey(): string {
		$settings = $this->all();

		return isset( $settings['amalfi_license_key'] ) ? trim( (string) $settings['amalfi_license_key'] ) : '';
	}

	/**
	 * Return the stored license status.
	 */
	public function licenseStatus(): string {
		$settings = $this->all();

		return isset( $settings['amalfi_license_status'] ) ? (string) $settings['amalfi_license_status'] : self::STATUS_INACTIVE;
	}

	/**
	 * Whether the license is currently valid and a key is present.
	 */
	public function hasValidLicense(): bool {
		return self::STATUS_VALID === $this->licenseStatus() && '' !== $this->licenseKey();
	}

	/**
	 * Store an activated license.
	 *
	 * @param string $key    The license key.
	 * @param string $status The license status returned by the store.
	 */
	public function storeLicense( string $key, string $status ): void {
		$this->save(
			array(
				'amalfi_license_key'    => $key,
				'amalfi_license_status' => $status,
			)
		);
	}

	/**
	 * Clear the stored license.
	 */
	public function clearLicense(): void {
		$this->save(
			array(
				'amalfi_license_key'    => '',
				'amalfi_license_status' => self::STATUS_INACTIVE,
			)
		);
	}

	/**
	 * The default settings shape.
	 *
	 * @return array<string, mixed>
	 */
	private function defaults(): array {
		return array(
			'amalfi_license_key'    => '',
			'amalfi_license_status' => self::STATUS_INACTIVE,
			'amalfi_demo_content'   => array(
				'categories' => 1,
				'tags'       => 1,
				'image'      => 1,
				'posts'      => 1,
				'pages'      => 1,
			),
		);
	}
}
