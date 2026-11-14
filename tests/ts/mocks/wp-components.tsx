/**
 * Lightweight stand-ins for the `@wordpress/components` used by the admin app.
 *
 * The real components are provided by WordPress at runtime (the `wp-components`
 * script is a build-time external), and loading the full library into jsdom is
 * both slow and brittle. These mocks render the minimal accessible DOM the
 * panels rely on — a labelled input, a checkbox, a button, a notice — so tests
 * can exercise our own logic (state, REST calls, conditional rendering) without
 * depending on WordPress component internals.
 */
import * as React from 'react';

/* eslint-disable @typescript-eslint/no-explicit-any */

export const BaseControl = ( { label, children }: any ) => (
	<div>
		{ label ? <span>{ label }</span> : null }
		{ children }
	</div>
);

export const TextControl = ( {
	label,
	value,
	onChange,
	placeholder,
	disabled,
	readOnly,
}: any ) => (
	<label>
		{ label }
		<input
			type="text"
			value={ value }
			placeholder={ placeholder }
			disabled={ disabled }
			readOnly={ readOnly }
			onChange={ ( event: React.ChangeEvent< HTMLInputElement > ) =>
				onChange?.( event.target.value )
			}
		/>
	</label>
);

export const ToggleControl = ( { label, checked, onChange }: any ) => (
	<label>
		{ label }
		<input
			type="checkbox"
			checked={ !! checked }
			onChange={ ( event: React.ChangeEvent< HTMLInputElement > ) =>
				onChange?.( event.target.checked )
			}
		/>
	</label>
);

export const Button = ( { children, onClick, disabled }: any ) => (
	<button type="button" onClick={ onClick } disabled={ disabled }>
		{ children }
	</button>
);

export const Notice = ( { children, onRemove }: any ) => (
	<div role="status">
		<span>{ children }</span>
		{ onRemove ? (
			<button type="button" onClick={ onRemove }>
				Dismiss
			</button>
		) : null }
	</div>
);

export const Spinner = () => <span data-testid="spinner" />;

export const Flex = ( { children }: any ) => <div>{ children }</div>;
