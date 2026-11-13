/**
 * Shared types for the Theme Setup admin app.
 *
 * These mirror the data shapes produced on the PHP side: the bootstrap object
 * injected via `window.amalfiThemeSetup` and the REST responses returned by the
 * demo importer and the license client.
 */

/**
 * A single importable demo content part (`src/DemoContent/Importer::parts()`).
 */
export interface DemoPart {
	key: string;
	label: string;
	count: number;
}

/**
 * The current license state (`src/Licensing/LicenseClient::state()`).
 */
export interface LicenseState {
	active: boolean;
	status: string;
	keyMasked: string;
}

/**
 * The REST response returned when activating or deactivating a license.
 */
export interface LicenseResponse {
	active: boolean;
	status: string;
	keyMasked?: string;
	message: string;
	type: 'success' | 'error';
}

/**
 * The payload sent to the license REST route.
 */
export type LicenseRequest =
	| { action: 'activate'; key: string }
	| { action: 'deactivate' };

/**
 * A single demo import task result (`src/DemoContent/Importer`).
 */
export interface ImportResult {
	task: string;
	label?: string;
	status: 'success' | 'error';
	message: string;
}

/**
 * The REST response returned when running the demo importer.
 */
export interface DemoResponse {
	results: ImportResult[];
}

/**
 * The bootstrap data injected into the page by `src/Admin/Assets.php`.
 */
export interface ThemeSetupBootstrap {
	restNamespace?: string;
	demoParts?: DemoPart[];
	license?: Partial< LicenseState >;
}

/**
 * An admin-side notice shown by a panel.
 */
export interface PanelNotice {
	status: 'success' | 'error';
	message: string;
}

declare global {
	interface Window {
		amalfiThemeSetup?: ThemeSetupBootstrap;
	}
}
