// Tab List — the row of tab buttons (`.wp-block-tab-list button`). Each tab is
// styled as a Button: the active tab (`[aria-selected="true"]`) reads like the
// default Button block (filled primary-950 with white text), and the inactive
// tabs read like the Button block's Outline style (transparent box with a
// primary-950 border and text that fills on hover / keyboard focus).
//
// The box is deliberately more compact than a real Button so the tabs match the
// size of the Post Terms / Tag Cloud pills: the same body-xs uppercase label and
// the same tight `spacing-150 / spacing-200` padding (minus the 1px border), not
// the Button's roomier `spacing-200 / spacing-300`.
//
// All of this lives in `css` because the design needs per-state selectors a
// design key can't reach: the `[aria-selected="true"]` active tab, the non-link
// `<button>` elements, and core's active-tab underline. The tab buttons' default
// CSS is emitted inside `:where(…)` (0-0-0), so these `:root :where(…)` rules
// (0-1-0) override the box, colour and padding without `!important`; only core's
// active underline (`…[aria-selected=true]::before`, 0-1-1) needs it. Each
// selector is its own rule block — WordPress's `&`-scoping keeps only the last
// selector of a comma-separated list.
//
// `blockGap` sets the gap between tabs (the tab list is a flex/wrap container).
const CSS =
	// Base box + label: every tab is an outline button at the pill size.
	'& button{' +
	'border:1px solid var(--wp--preset--color--primary-950);' +
	'border-radius:0;' +
	'padding:calc(var(--wp--preset--spacing--150) - 1px) ' +
	'calc(var(--wp--preset--spacing--200) - 1px);' +
	'background-color:transparent;' +
	'color:var(--wp--preset--color--primary-950);' +
	'font-size:var(--wp--preset--font-size--body-xs);' +
	'font-weight:700;' +
	'letter-spacing:0.12rem;' +
	'line-height:var(--wp--custom--line-height--display);' +
	'text-transform:uppercase;' +
	'transition:all 0.2s ease-in-out' +
	'}' +
	// Inactive tabs fill on hover / keyboard focus, like the Outline Button.
	'& button:hover{' +
	'background-color:var(--wp--preset--color--primary-950);' +
	'color:var(--wp--preset--color--white)' +
	'}' +
	'& button:focus-visible{' +
	'background-color:var(--wp--preset--color--primary-950);' +
	'color:var(--wp--preset--color--white)' +
	'}' +
	// Active tab: the filled default Button look (keeps the same border, so the
	// box stays the same size as the inactive tabs).
	'& button[aria-selected="true"]{' +
	'background-color:var(--wp--preset--color--primary-950);' +
	'color:var(--wp--preset--color--white)' +
	'}' +
	// Drop core's active-tab underline — the filled box is the active affordance.
	// Core sets it at 0-1-1, past this 0-1-0 rule, so it needs `!important` (no
	// editor control for it).
	'& button[aria-selected="true"]::before{display:none !important}';

module.exports = {
	spacing: {
		blockGap: 'var:preset|spacing|100',
	},
	css: CSS,
};
