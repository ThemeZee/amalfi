/**
 * License Settings section: activate or deactivate the theme license.
 */
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { TextControl, Button, Notice, Spinner, Flex } from '@wordpress/components';

export default function LicensePanel( { initialState, restNamespace } ) {
	const [ state, setState ] = useState( {
		active: !! initialState.active,
		status: initialState.status || 'inactive',
		keyMasked: initialState.keyMasked || '',
	} );
	const [ key, setKey ] = useState( '' );
	const [ isBusy, setIsBusy ] = useState( false );
	const [ notice, setNotice ] = useState( null );

	const request = async ( data ) => {
		setIsBusy( true );
		setNotice( null );

		try {
			const response = await apiFetch( {
				path: `/${ restNamespace }/license`,
				method: 'POST',
				data,
			} );
			setState( {
				active: !! response.active,
				status: response.status,
				keyMasked: response.keyMasked || '',
			} );
			setNotice( {
				status: response.type === 'success' ? 'success' : 'error',
				message: response.message,
			} );
			if ( response.active ) {
				setKey( '' );
			}
		} catch ( error ) {
			setNotice( {
				status: 'error',
				message:
					error.message ||
					__( 'The request could not be completed.', 'amalfi' ),
			} );
		} finally {
			setIsBusy( false );
		}
	};

	return (
		<div className="amalfi-theme-setup__section">
			<p>
				<strong>{ __( 'Status:', 'amalfi' ) }</strong>{ ' ' }
				<span
					className={ `amalfi-license-badge amalfi-license-badge--${
						state.active ? 'active' : 'inactive'
					}` }
				>
					{ state.active
						? __( 'Active', 'amalfi' )
						: __( 'Inactive', 'amalfi' ) }
				</span>
			</p>
			<p className="amalfi-theme-setup__hint">
				{ state.active
					? __( 'You are receiving updates.', 'amalfi' )
					: __(
							'An active license key is needed to install updates and receive support.',
							'amalfi'
					  ) }
			</p>

			{ state.active ? (
				<>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'License Key', 'amalfi' ) }
						value={ state.keyMasked }
						readOnly
						disabled
					/>
					<Flex justify="flex-start" className="amalfi-theme-setup__actions">
						<Button
							variant="secondary"
							isDestructive
							onClick={ () => request( { action: 'deactivate' } ) }
							disabled={ isBusy }
							__next40pxDefaultSize
						>
							{ isBusy
								? __( 'Working…', 'amalfi' )
								: __( 'Deactivate License', 'amalfi' ) }
						</Button>
						{ isBusy && <Spinner /> }
					</Flex>
				</>
			) : (
				<>
					<TextControl
						__nextHasNoMarginBottom
						label={ __( 'License Key', 'amalfi' ) }
						value={ key }
						onChange={ setKey }
						placeholder={ __( 'Enter your license key', 'amalfi' ) }
					/>
					<Flex justify="flex-start" className="amalfi-theme-setup__actions">
						<Button
							variant="primary"
							onClick={ () => request( { action: 'activate', key } ) }
							disabled={ isBusy || ! key }
							__next40pxDefaultSize
						>
							{ isBusy
								? __( 'Activating…', 'amalfi' )
								: __( 'Activate License', 'amalfi' ) }
						</Button>
						{ isBusy && <Spinner /> }
					</Flex>
				</>
			) }

			{ notice && (
				<Notice
					status={ notice.status }
					isDismissible={ false }
					className="amalfi-theme-setup__notice"
				>
					{ notice.message }
				</Notice>
			) }
		</div>
	);
}
