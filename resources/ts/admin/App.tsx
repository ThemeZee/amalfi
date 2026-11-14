/**
 * Demo Content / License Settings sections.
 *
 * Only this interactive part is hydrated client-side; the rest of the Theme
 * Setup page (header, links, screenshot, documentation) is server-rendered.
 * Each section is a plain heading plus its fields on the default admin
 * background — no card or collapsible wrapper.
 */
import { __ } from '@wordpress/i18n';

import DemoContentPanel from './DemoContentPanel';
import LicensePanel from './LicensePanel';
import type { ThemeSetupBootstrap } from './types';

export default function App() {
	const bootstrap: ThemeSetupBootstrap = window.amalfiThemeSetup || {};
	const restNamespace = bootstrap.restNamespace || 'amalfi/v1';

	return (
		<div className="amalfi-theme-setup__columns">
			<section className="amalfi-theme-setup__group">
				<h3>{ __( 'Demo Content', 'amalfi' ) }</h3>
				<DemoContentPanel
					parts={ bootstrap.demoParts || [] }
					restNamespace={ restNamespace }
				/>
			</section>

			<section className="amalfi-theme-setup__group">
				<h3>{ __( 'License Settings', 'amalfi' ) }</h3>
				<LicensePanel
					initialState={ bootstrap.license || {} }
					restNamespace={ restNamespace }
				/>
			</section>
		</div>
	);
}
