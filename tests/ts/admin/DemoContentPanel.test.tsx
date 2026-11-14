/**
 * Tests for the Demo Content panel.
 */
import { act, render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import apiFetch from '@wordpress/api-fetch';

import DemoContentPanel from '../../../resources/ts/admin/DemoContentPanel';
import type { DemoPart } from '../../../resources/ts/admin/types';

jest.mock( '@wordpress/api-fetch' );

const mockApiFetch = apiFetch as unknown as jest.Mock;

const parts: DemoPart[] = [
	{ key: 'posts', label: 'Blog Posts', count: 12 },
	{ key: 'pages', label: 'Static Pages', count: 3 },
];

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

describe( 'DemoContentPanel', () => {
	it( 'renders a toggle per part with its label and count', () => {
		render(
			<DemoContentPanel parts={ parts } restNamespace="amalfi/v1" />
		);

		expect( screen.getByText( 'Blog Posts (12)' ) ).toBeInTheDocument();
		expect( screen.getByText( 'Static Pages (3)' ) ).toBeInTheDocument();
	} );

	it( 'posts the selected parts to the demo route and shows results', async () => {
		const fetch = pendingFetch();

		render(
			<DemoContentPanel parts={ parts } restNamespace="amalfi/v1" />
		);

		await userEvent.click(
			screen.getByRole( 'button', { name: 'Import Content' } )
		);
		await fetch.resolveWith( {
			results: [
				{
					task: 'posts',
					status: 'success',
					message: 'Blog Posts imported.',
				},
			],
		} );

		expect(
			screen.getByText( 'Blog Posts imported.' )
		).toBeInTheDocument();
		expect( mockApiFetch ).toHaveBeenCalledWith( {
			path: '/amalfi/v1/demo',
			method: 'POST',
			data: { parts: { posts: true, pages: true } },
		} );
	} );

	it( 'excludes parts that were toggled off', async () => {
		const fetch = pendingFetch();

		render(
			<DemoContentPanel parts={ parts } restNamespace="amalfi/v1" />
		);

		await userEvent.click( screen.getByLabelText( 'Static Pages (3)' ) );
		await userEvent.click(
			screen.getByRole( 'button', { name: 'Import Content' } )
		);
		await fetch.resolveWith( { results: [] } );

		expect( mockApiFetch ).toHaveBeenCalledWith(
			expect.objectContaining( {
				data: { parts: { posts: true, pages: false } },
			} )
		);
	} );

	it( 'disables the import button when every part is toggled off', async () => {
		render(
			<DemoContentPanel parts={ parts } restNamespace="amalfi/v1" />
		);

		await userEvent.click( screen.getByLabelText( 'Blog Posts (12)' ) );
		await userEvent.click( screen.getByLabelText( 'Static Pages (3)' ) );

		expect(
			screen.getByRole( 'button', { name: 'Import Content' } )
		).toBeDisabled();
	} );

	it( 'shows an error notice when the request rejects', async () => {
		const fetch = pendingFetch();

		render(
			<DemoContentPanel parts={ parts } restNamespace="amalfi/v1" />
		);

		await userEvent.click(
			screen.getByRole( 'button', { name: 'Import Content' } )
		);
		await fetch.rejectWith( new Error( 'Server exploded' ) );

		expect( screen.getByText( 'Server exploded' ) ).toBeInTheDocument();
	} );
} );
