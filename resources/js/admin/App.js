/**
 * Demo Content / License Settings panels.
 *
 * Only this interactive part is hydrated client-side; the rest of the Theme
 * Setup page (header, links, screenshot, documentation) is server-rendered.
 */
import { __ } from '@wordpress/i18n';
import { Panel, PanelBody } from '@wordpress/components';

import DemoContentPanel from './DemoContentPanel';
import LicensePanel from './LicensePanel';

const bootstrap = window.amalfiThemeSetup || {};

export default function App() {
	const restNamespace = bootstrap.restNamespace || 'amalfi/v1';

	return (
		<Panel>
			<PanelBody title={ __( 'Demo Content', 'amalfi' ) } initialOpen={ true }>
				<DemoContentPanel
					parts={ bootstrap.demoParts || [] }
					restNamespace={ restNamespace }
				/>
			</PanelBody>
			<PanelBody title={ __( 'License Settings', 'amalfi' ) } initialOpen={ false }>
				<LicensePanel
					initialState={ bootstrap.license || {} }
					restNamespace={ restNamespace }
				/>
			</PanelBody>
		</Panel>
	);
}
