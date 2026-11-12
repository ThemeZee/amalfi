/**
 * Theme Setup application shell.
 */
import { __, sprintf } from '@wordpress/i18n';
import { Panel, PanelBody } from '@wordpress/components';

import DemoContentPanel from './DemoContentPanel';
import LicensePanel from './LicensePanel';

const bootstrap = window.amalfiThemeSetup || {};

export default function App() {
	const themeName = bootstrap.themeName || 'Amalfi';
	const version = bootstrap.version || '';
	const restNamespace = bootstrap.restNamespace || 'amalfi/v1';

	return (
		<>
			<h1 className="amalfi-theme-setup__title">
				{ version
					? /* translators: %1$s: theme name. %2$s: version. */
					  sprintf( __( '%1$s %2$s', 'amalfi' ), themeName, version )
					: themeName }
			</h1>
			<p className="amalfi-theme-setup__intro">
				{ __(
					'Import the demo content to get started quickly, and activate your license key to receive updates and support.',
					'amalfi'
				) }
			</p>

			<Panel className="amalfi-theme-setup__panel">
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
		</>
	);
}
