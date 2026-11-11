// Default styling for the Navigation block. A single `elements.link` entry
// styles every navigation link (top-level items and submenu items) in one
// place: no underline by default, underlined on hover.
//
// The "Main Navigation" block style (registered in src/Setup/BlockStyle.php)
// gives a header navigation a distinct look while other navigations — e.g. the
// footer — keep the default above. Everything that distinguishes it needs the
// variation's `css` property (preset values as var(--wp--preset--…)), because
// no design key can express it:
//   - uppercase/semibold/smaller applies to TOP-LEVEL links only — a design key
//     cannot target direct children versus links nested inside a dropdown;
//   - the hover colour must be set on the `<li>` (see below), not the link;
//   - the dropdown panel is the `…__submenu-container` wrapper, which is neither
//     a block nor a theme.json element, so its no-border / soft-green fill /
//     rounded corners can only be reached by selector.
// The base link colour stays the nav default ("dark green / black, like now")
// via inheritance, so no colour key is set for it.
const MAIN_NAVIGATION_CSS =
	// Top-level links only: a buttonish uppercase, semibold, tracked label at the
	// small button font size.
	'& .wp-block-navigation__container > li > .wp-block-navigation-item__content{' +
	'text-transform:uppercase;font-weight:600;letter-spacing:0.12rem;' +
	'font-size:var(--wp--preset--font-size--body-xs)' +
	'}' +
	// Hover / keyboard-focus colour for top-level items. Core pins the link
	// itself to `color:inherit` at 0-3-0 (`.…-item__content.…-item__content`),
	// which the variation's `:root :where(…)` link rules (0-1-0) can't beat — so
	// set the colour on the `<li>` and let the link inherit it. No `!important`
	// needed: the `<li>` carries no competing colour rule. Each state is its own
	// rule block: WordPress's `&`-scoping drops all but the last of a single
	// comma-separated selector, so the two states must not share one.
	'& .wp-block-navigation__container > li:hover{' +
	'color:var(--wp--preset--color--primary-700)' +
	'}' +
	'& .wp-block-navigation__container > li:focus-within{' +
	'color:var(--wp--preset--color--primary-700)' +
	'}' +
	// Slightly larger dropdown chevron on top-level toggles (the `<button>` is a
	// direct child of the top-level `<li>`, so this leaves any nested icon alone),
	// nudged up 1px to sit better against the uppercase label. The
	// inner `<svg>` is `width/height:inherit`, so sizing the button is enough.
	// Core sets 0.6em at 0-2-0 (`.wp-block-navigation .…__submenu-icon`), past this
	// `:root :where(…)` rule (0-1-0), so the size needs `!important` (no editor
	// control); the relative nudge has no competing rule.
	'& .wp-block-navigation__container > li > .wp-block-navigation__submenu-icon{' +
	'width:0.75em !important;height:0.75em !important;' +
	'position:relative;top:-1px' +
	'}' +
	// Dropdown panel: drop core's border, green fill, rounded corners, and drop
	// it a gap below the top-level item.
	// Core styles the border and white fill at `.wp-block-navigation:not(
	// .has-background) .wp-block-navigation__submenu-container` (0-3-0), and pins
	// `margin-top:0` via `.wp-block-navigation ul` (0-1-1). WordPress emits this
	// variation `css` at `:root :where(…)` (0-1-0), which there is no way to raise
	// past, so overriding those core defaults needs `!important` — the declaration
	// that beats 0-3-0/0-1-1. `border-radius` is a UA default (0-0-0), so it wins
	// without. See CLAUDE.md §3 on this exact trade-off.
	'& .wp-block-navigation__submenu-container{' +
	'border:none !important;' +
	'background-color:var(--wp--preset--color--primary-200) !important;' +
	'border-radius:8px;' +
	// A little vertical padding so the items sit clear of the rounded corners,
	// which makes the 8px radius read. No competing core rule, so no !important.
	'padding-top:calc(var(--wp--preset--spacing--100) + 4px);' +
	'padding-bottom:calc(var(--wp--preset--spacing--100) + 4px);' +
	'margin-top:var(--wp--preset--spacing--150) !important' +
	'}' +
	// Keep hover alive across that gap. The panel sits `--spacing--150` below the
	// item, but the empty gap is on no `<li>` descendant, so moving the pointer
	// down through it would drop `:hover`/`:focus-within` and close the menu. A
	// transparent `::before` on the panel (itself inside the `<li>`) fills exactly
	// that gap, so the pointer stays within the item and the dropdown stays open.
	'& .wp-block-navigation__submenu-container::before{' +
	'content:"";position:absolute;left:0;right:0;' +
	'top:calc(0px - var(--wp--preset--spacing--150));' +
	'height:var(--wp--preset--spacing--150)' +
	'}' +
	// Dropdown links: a touch smaller than the default nav text and semibold.
	// Size/weight have no competing core rule, so they win at 0-1-0. The roomier
	// left/right padding does: core sets it at 0-4-0 (`.wp-block-navigation
	// .has-child …__submenu-container > .…-item__content`), past this `:root
	// :where(…)` rule, so those two declarations need `!important` (no editor
	// control for nav-link padding). Vertical padding is left to core.
	'& .wp-block-navigation__submenu-container .wp-block-navigation-item__content{' +
	'font-size:var(--wp--preset--font-size--body-sm);' +
	'font-weight:600;' +
	'padding-left:var(--wp--preset--spacing--300) !important;' +
	'padding-right:var(--wp--preset--spacing--300) !important' +
	'}' +
	// Hover / keyboard-focus colour for dropdown items — set on the `<li>` (the
	// link stays core's `color:inherit`), one rule block per state. No competing
	// colour rule on these items, so no !important.
	'& .wp-block-navigation__submenu-container > li:hover{' +
	'color:var(--wp--preset--color--primary-700)' +
	'}' +
	'& .wp-block-navigation__submenu-container > li:focus-within{' +
	'color:var(--wp--preset--color--primary-700)' +
	'}';

module.exports = {
	elements: {
		link: {
			typography: {
				textDecoration: 'none',
			},
			':hover': {
				typography: {
					textDecoration: 'underline',
				},
			},
		},
	},
	variations: {
		'main-navigation': {
			spacing: {
				// Roomier gap between top-level items than the default nav.
				blockGap: 'var:preset|spacing|300',
			},
			elements: {
				link: {
					// No underline — the hover colour shift (set in the `css`
					// above, on the `<li>`) is the only hover affordance here.
					typography: {
						textDecoration: 'none',
					},
					':hover': {
						typography: {
							textDecoration: 'none',
						},
					},
				},
			},
			css: MAIN_NAVIGATION_CSS,
		},
	},
};
