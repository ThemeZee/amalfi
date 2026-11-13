<?php
/**
 * Central theme configuration for the Admin module.
 *
 * @package ThemeZee\Amalfi
 */

declare(strict_types=1);

namespace ThemeZee\Amalfi\Admin;

/**
 * Immutable value object holding the constants the Admin services share.
 *
 * This replaces the loose `define()` constants used in the procedural themes
 * (e.g. AMALFI_THEME_NAME / _ID / _VERSION / _STORE_URL) with a single,
 * injectable source of truth. Instances are created in {@see Module::services()}
 * from the Packable theme properties, so name, version, author and text domain
 * stay in sync with the theme header without being duplicated here.
 */
final class ThemeConfig {

	/**
	 * The ThemeZee store (Easy Digital Downloads) endpoint used for licensing
	 * and update checks.
	 */
	public const STORE_URL = 'https://themezee.com';

	/**
	 * The theme's download ID in the ThemeZee store.
	 *
	 * Set this to the Easy Digital Downloads download ID for the Amalfi theme
	 * so license activation and update checks resolve to the right product.
	 */
	public const ITEM_ID = 267408;

	/**
	 * Constructor.
	 *
	 * @param string $name        Human readable theme name (e.g. "Amalfi").
	 * @param string $slug        Theme slug / stylesheet directory name.
	 * @param string $version     Current theme version.
	 * @param string $author      Theme author.
	 * @param string $textDomain  Theme text domain.
	 * @param string $basePath    Absolute path to the theme root (no trailing slash).
	 * @param string $description Theme description.
	 * @param string $uri         Theme home page URL.
	 * @param int    $itemId      Store download ID.
	 * @param string $storeUrl    Licensing / update API endpoint.
	 */
	public function __construct(
		private readonly string $name,
		private readonly string $slug,
		private readonly string $version,
		private readonly string $author,
		private readonly string $textDomain,
		private readonly string $basePath,
		private readonly string $description = '',
		private readonly string $uri = '',
		private readonly int $itemId = self::ITEM_ID,
		private readonly string $storeUrl = self::STORE_URL,
	) {
	}

	/**
	 * The theme description.
	 */
	public function description(): string {
		return $this->description;
	}

	/**
	 * The theme home page URL.
	 */
	public function uri(): string {
		return $this->uri;
	}

	/**
	 * The human readable theme name.
	 */
	public function name(): string {
		return $this->name;
	}

	/**
	 * The theme slug (stylesheet directory name).
	 */
	public function slug(): string {
		return $this->slug;
	}

	/**
	 * The current theme version.
	 */
	public function version(): string {
		return $this->version;
	}

	/**
	 * The theme author.
	 */
	public function author(): string {
		return $this->author;
	}

	/**
	 * The theme text domain.
	 */
	public function textDomain(): string {
		return $this->textDomain;
	}

	/**
	 * Absolute path to the theme root directory (no trailing slash).
	 */
	public function basePath(): string {
		return $this->basePath;
	}

	/**
	 * The store download ID.
	 */
	public function itemId(): int {
		return $this->itemId;
	}

	/**
	 * The licensing / update API endpoint.
	 */
	public function storeUrl(): string {
		return $this->storeUrl;
	}

	/**
	 * The option name the theme settings are stored under.
	 */
	public function optionName(): string {
		return 'amalfi_theme_settings';
	}

	/**
	 * The slug of the admin page under Appearance.
	 */
	public function pageSlug(): string {
		return 'amalfi-theme';
	}

	/**
	 * The capability required to view the admin page and manage settings.
	 */
	public function capability(): string {
		return 'manage_options';
	}

	/**
	 * The admin URL of the theme settings page.
	 */
	public function pageUrl(): string {
		return admin_url( 'themes.php?page=' . $this->pageSlug() );
	}
}
