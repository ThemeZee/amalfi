/**
 * Tests for the Theme Setup App shell.
 */
import { render, screen } from '@testing-library/react';

import App from '../../../resources/ts/admin/App';

jest.mock( '@wordpress/api-fetch' );

afterEach( () => {
	delete window.amalfiThemeSetup;
} );

describe( 'App', () => {
	it( 'renders both section headings from the bootstrap data', () => {
		window.amalfiThemeSetup = {
			restNamespace: 'amalfi/v1',
			demoParts: [ { key: 'posts', label: 'Blog Posts', count: 2 } ],
			license: { active: false, status: 'inactive', keyMasked: '' },
		};

		render( <App /> );

		expect(
			screen.getByRole( 'heading', { name: 'Demo Content' } )
		).toBeInTheDocument();
		expect(
			screen.getByRole( 'heading', { name: 'License Settings' } )
		).toBeInTheDocument();
		expect( screen.getByText( 'Blog Posts (2)' ) ).toBeInTheDocument();
	} );

	it( 'falls back to safe defaults when the bootstrap global is missing', () => {
		render( <App /> );

		expect(
			screen.getByRole( 'heading', { name: 'Demo Content' } )
		).toBeInTheDocument();
		expect( screen.getByText( 'Inactive' ) ).toBeInTheDocument();
	} );
} );
