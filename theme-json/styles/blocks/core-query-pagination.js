// Default styling for the Pagination block, matching the Button block's
// Outline style: a 1px bordered link that fills with primary-950 on
// hover/active (and for the current page). WordPress's theme.json `css`
// processor drops all but the last selector in a comma group, so each
// selector is emitted as its own rule below.

const restButton = [
	'display:inline-block;',
	'border:1px solid currentColor;',
	'border-radius:0;',
	'padding:calc(var(--wp--preset--spacing--200) - 1px) calc(var(--wp--preset--spacing--300) - 1px);',
	'color:var(--wp--preset--color--primary-950);',
	'font-size:var(--wp--preset--font-size--body-xs);',
	'font-weight:700;',
	'letter-spacing:0.12rem;',
	'line-height:var(--wp--custom--line-height--display);',
	'text-transform:uppercase;',
	'text-decoration:none;',
	'transition:all 0.2s ease-in-out',
].join( '' );

const filledButton = [
	'background-color:var(--wp--preset--color--primary-950);',
	'color:var(--wp--preset--color--white);',
	'border-color:var(--wp--preset--color--primary-950)',
].join( '' );

const focusRing = 'outline:3px solid var(--wp--preset--color--primary-950);outline-offset:3px';

// Every pagination link/number gets the resting Outline button look.
const restTargets = [
	'.wp-block-query-pagination-previous',
	'.wp-block-query-pagination-next',
	'.wp-block-query-pagination-numbers .page-numbers:not(.dots)',
];

// Only the real links (not the current page or the dots) get interactive states.
const linkTargets = [
	'.wp-block-query-pagination-previous',
	'.wp-block-query-pagination-next',
	'.wp-block-query-pagination-numbers a.page-numbers:not(.dots)',
];

const rule = ( selector, declarations ) => `& ${ selector }{${ declarations }}`;

module.exports = {
	spacing: {
		blockGap: 'var:preset|spacing|200',
	},
	css: [
		// Lay the page numbers out with consistent spacing.
		'& .wp-block-query-pagination-numbers{display:flex;flex-wrap:wrap;align-items:center;gap:var(--wp--preset--spacing--200)}',

		// Resting Outline button look.
		...restTargets.map( ( selector ) => rule( selector, restButton ) ),

		// Hover fills like the Outline button.
		...linkTargets.map( ( selector ) => rule( `${ selector }:hover`, filledButton ) ),

		// Current page stays filled to mark the active page.
		rule( '.wp-block-query-pagination-numbers .page-numbers.current', filledButton ),

		// Keyboard focus ring matching the button element.
		...linkTargets.map( ( selector ) => rule( `${ selector }:focus-visible`, focusRing ) ),
	].join( '' ),
};
