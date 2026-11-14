/**
 * Tests for the License Settings panel.
 */
import { act, render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import apiFetch from '@wordpress/api-fetch';

import LicensePanel from '../../../resources/ts/admin/LicensePanel';

jest.mock( '@wordpress/api-fetch' );

const mockApiFetch = apiFetch as unknown as jest.Mock;

/**
 * Make the next apiFetch call hang until resolved/rejected here, so the
 * resulting React state updates can be flushed inside an `act` scope (avoiding
 * "not wrapped in act" warnings from the component's async click handler).
 */
function pendingFetch() {
	let settle: {
		resolve: ( value: unknown ) => void;
		reject: ( error: unknown ) => void;
	};

	mockApiFetch.mockImplementation(
		() =>
			new Promise( ( resolve, reject ) => {
				settle = { resolve, reject };
			} )
	);

	return {
		resolveWith: ( value: unknown ) =>
			act( async () => {
				settle.resolve( value );
			} ),
		rejectWith: ( error: unknown ) =>
			act( async () => {
				settle.reject( error );
			} ),
	};
}

afterEach( () => {
	jest.clearAllMocks();
} );

describe( 'LicensePanel (inactive)', () => {
	it( 'shows the inactive badge and a disabled activate button until a key is entered', () => {
		render(
			<LicensePanel initialState={ {} } restNamespace="amalfi/v1" />
		);

		expect( screen.getByText( 'Inactive' ) ).toBeInTheDocument();
		expect(
			screen.getByRole( 'button', { name: 'Activate License' } )
		).toBeDisabled();
	} );

	it( 'activates with the entered key and reflects the active response', async () => {
		const fetch = pendingFetch();

		render(
			<LicensePanel initialState={ {} } restNamespace="amalfi/v1" />
		);

		await userEvent.type(
			screen.getByLabelText( 'License Key' ),
			'SECRET-ABCDE'
		);
		await userEvent.click(
			screen.getByRole( 'button', { name: 'Activate License' } )
		);
		await fetch.resolveWith( {
			active: true,
			status: 'valid',
			keyMasked: '***** ABCDE',
			message: 'License was successfully activated.',
			type: 'success',
		} );

		expect( screen.getByText( 'Active' ) ).toBeInTheDocument();
		expect(
			screen.getByText( 'License was successfully activated.' )
		).toBeInTheDocument();
		expect(
			screen.getByRole( 'button', { name: 'Deactivate License' } )
		).toBeInTheDocument();
		expect( mockApiFetch ).toHaveBeenCalledWith( {
			path: '/amalfi/v1/license',
			method: 'POST',
			data: { action: 'activate', key: 'SECRET-ABCDE' },
		} );
	} );

	it( 'shows an error notice when activation fails', async () => {
		const fetch = pendingFetch();

		render(
			<LicensePanel initialState={ {} } restNamespace="amalfi/v1" />
		);

		await userEvent.type( screen.getByLabelText( 'License Key' ), 'nope' );
		await userEvent.click(
			screen.getByRole( 'button', { name: 'Activate License' } )
		);
		await fetch.resolveWith( {
			active: false,
			status: 'inactive',
			keyMasked: '',
			message: 'Invalid license key.',
			type: 'error',
		} );

		expect(
			screen.getByText( 'Invalid license key.' )
		).toBeInTheDocument();
		expect( screen.getByText( 'Inactive' ) ).toBeInTheDocument();
	} );

	it( 'surfaces a thrown error message', async () => {
		const fetch = pendingFetch();

		render(
			<LicensePanel initialState={ {} } restNamespace="amalfi/v1" />
		);

		await userEvent.type( screen.getByLabelText( 'License Key' ), 'key' );
		await userEvent.click(
			screen.getByRole( 'button', { name: 'Activate License' } )
		);
		await fetch.rejectWith( new Error( 'Network down' ) );

		expect( screen.getByText( 'Network down' ) ).toBeInTheDocument();
	} );
} );

describe( 'LicensePanel (active)', () => {
	const activeState = {
		active: true,
		status: 'valid',
		keyMasked: '***** WXYZ',
	};

	it( 'shows the active badge and the masked key', () => {
		render(
			<LicensePanel
				initialState={ activeState }
				restNamespace="amalfi/v1"
			/>
		);

		expect( screen.getByText( 'Active' ) ).toBeInTheDocument();
		expect( screen.getByDisplayValue( '***** WXYZ' ) ).toBeInTheDocument();
	} );

	it( 'deactivates without sending a key', async () => {
		const fetch = pendingFetch();

		render(
			<LicensePanel
				initialState={ activeState }
				restNamespace="amalfi/v1"
			/>
		);

		await userEvent.click(
			screen.getByRole( 'button', { name: 'Deactivate License' } )
		);
		await fetch.resolveWith( {
			active: false,
			status: 'inactive',
			keyMasked: '',
			message: 'License was deactivated.',
			type: 'success',
		} );

		expect( screen.getByText( 'Inactive' ) ).toBeInTheDocument();
		expect( mockApiFetch ).toHaveBeenCalledWith( {
			path: '/amalfi/v1/license',
			method: 'POST',
			data: { action: 'deactivate' },
		} );
	} );
} );
