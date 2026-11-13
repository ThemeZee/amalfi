/**
 * Theme Setup application shell.
 *
 * Lays out a theme-info style page: a welcome header with the theme
 * description and links, a "Getting started" area with the collapsible
 * Demo Content and License Settings panels beside the theme screenshot, and a
 * documentation section below.
 */
import { __, sprintf } from '@wordpress/i18n';
import { Panel, PanelBody, Button } from '@wordpress/components';

import DemoContentPanel from './DemoContentPanel';
import LicensePanel from './LicensePanel';

const bootstrap = window.amalfiThemeSetup || {};

export default function App() {
	const themeName = bootstrap.themeName || 'Amalfi';
	const version = bootstrap.version || '';
	const restNamespace = bootstrap.restNamespace || 'amalfi/v1';
	const links = bootstrap.links || [];
	const screenshot = bootstrap.screenshot || '';
	const docsUrl = bootstrap.docsUrl || '';

	return (
		<div className="amalfi-theme-info">
			<h1 className="amalfi-theme-info__title">
				{ sprintf(
					/* translators: %1$s: theme name. %2$s: version. */
					__( 'Welcome to %1$s %2$s', 'amalfi' ),
					themeName,
					version
				) }
			</h1>

			{ bootstrap.description && (
				<p className="amalfi-theme-info__description">
					{ bootstrap.description }
				</p>
			) }

			<hr />

			<p className="amalfi-theme-info__links">
				<strong>{ __( 'Theme Links:', 'amalfi' ) }</strong>
				{ links.map( ( link ) => (
					<a
						key={ link.url }
						href={ link.url }
						target="_blank"
						rel="noreferrer"
					>
						{ link.label }
					</a>
				) ) }
			</p>

			<hr />

			<section className="amalfi-theme-info__getting-started">
				<h2>
					{ sprintf(
						/* translators: %s: theme name. */
						__( 'Getting started with %s', 'amalfi' ),
						themeName
					) }
				</h2>

				<div className="amalfi-theme-info__columns">
					<div className="amalfi-theme-info__main">
						<Panel>
							<PanelBody
								title={ __( 'Demo Content', 'amalfi' ) }
								initialOpen={ true }
							>
								<DemoContentPanel
									parts={ bootstrap.demoParts || [] }
									restNamespace={ restNamespace }
								/>
							</PanelBody>
							<PanelBody
								title={ __( 'License Settings', 'amalfi' ) }
								initialOpen={ false }
							>
								<LicensePanel
									initialState={ bootstrap.license || {} }
									restNamespace={ restNamespace }
								/>
							</PanelBody>
						</Panel>
					</div>

					<div className="amalfi-theme-info__side">
						{ screenshot ? (
							<img
								className="amalfi-theme-info__screenshot"
								src={ screenshot }
								alt={ sprintf(
									/* translators: %s: theme name. */
									__( '%s theme screenshot', 'amalfi' ),
									themeName
								) }
							/>
						) : (
							<div
								className="amalfi-theme-info__screenshot amalfi-theme-info__screenshot--placeholder"
								aria-hidden="true"
							>
								<span>{ themeName }</span>
							</div>
						) }
					</div>
				</div>
			</section>

			<hr />

			<section className="amalfi-theme-info__documentation">
				<h2>{ __( 'Theme Documentation', 'amalfi' ) }</h2>
				<p className="amalfi-theme-info__about">
					{ __(
						'Need help to set up and configure this theme? We have you covered. Check out the extensive theme documentation on our website.',
						'amalfi'
					) }
				</p>
				{ docsUrl && (
					<Button
						variant="secondary"
						href={ docsUrl }
						target="_blank"
						rel="noreferrer"
						__next40pxDefaultSize
					>
						{ sprintf(
							/* translators: %s: theme name. */
							__( "View %s's documentation", 'amalfi' ),
							themeName
						) }
					</Button>
				) }
			</section>
		</div>
	);
}
