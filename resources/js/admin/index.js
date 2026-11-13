/**
 * Theme Setup admin page entry.
 *
 * Mounts the React application onto the root element rendered by AdminPage.
 */
import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';

import App from './App';
import './admin.scss';

domReady( () => {
	const root = document.getElementById( 'amalfi-demo-license' );

	if ( root ) {
		createRoot( root ).render( <App /> );
	}
} );
