/**
 * Demo Content section: choose parts and run the importer.
 */
import { useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import {
	ToggleControl,
	Button,
	Notice,
	Spinner,
	Flex,
} from '@wordpress/components';

export default function DemoContentPanel( { parts, restNamespace } ) {
	const [ selected, setSelected ] = useState( () =>
		Object.fromEntries( parts.map( ( part ) => [ part.key, true ] ) )
	);
	const [ isBusy, setIsBusy ] = useState( false );
	const [ results, setResults ] = useState( null );

	const anySelected = Object.values( selected ).some( Boolean );

	const toggle = ( key ) => ( value ) =>
		setSelected( ( current ) => ( { ...current, [ key ]: value } ) );

	const runImport = async () => {
		setIsBusy( true );
		setResults( null );

		try {
			const response = await apiFetch( {
				path: `/${ restNamespace }/demo`,
				method: 'POST',
				data: { parts: selected },
			} );
			setResults( response.results || [] );
		} catch ( error ) {
			setResults( [
				{
					task: 'error',
					status: 'error',
					message:
						error.message ||
						__( 'The import could not be completed.', 'amalfi' ),
				},
			] );
		} finally {
			setIsBusy( false );
		}
	};

	return (
		<div className="amalfi-theme-setup__section">
			<p>
				{ __(
					'Select the content to import, then run the importer.',
					'amalfi'
				) }
			</p>

			<div className="amalfi-theme-setup__toggles">
				{ parts.map( ( part ) => (
					<ToggleControl
						key={ part.key }
						__nextHasNoMarginBottom
						label={ sprintf(
							/* translators: %1$s: content type label. %2$d: number of items. */
							__( '%1$s (%2$d)', 'amalfi' ),
							part.label,
							part.count
						) }
						checked={ !! selected[ part.key ] }
						onChange={ toggle( part.key ) }
					/>
				) ) }
			</div>

			<Flex justify="flex-start" className="amalfi-theme-setup__actions">
				<Button
					variant="primary"
					onClick={ runImport }
					disabled={ isBusy || ! anySelected }
					__next40pxDefaultSize
				>
					{ isBusy
						? __( 'Importing…', 'amalfi' )
						: __( 'Import Content', 'amalfi' ) }
				</Button>
				{ isBusy && <Spinner /> }
			</Flex>

			{ results &&
				results.map( ( result ) => (
					<Notice
						key={ result.task }
						status={ result.status === 'success' ? 'success' : 'error' }
						onRemove={ () =>
							setResults( ( current ) =>
								current.filter( ( item ) => item.task !== result.task )
							)
						}
						className="amalfi-theme-setup__notice"
					>
						{ result.message }
					</Notice>
				) ) }
		</div>
	);
}
