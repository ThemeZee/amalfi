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

// Top-level items reach the container by one of two chains, so every top-level
// rule has to be emitted for both:
//   - a hand-built menu item (`core/navigation-link`) is an `<li>` placed
//     DIRECTLY inside `.wp-block-navigation__container`;
//   - the Page List block (`core/page-list`), which is what a fresh install's
//     navigation shows, wraps its items in an extra `<ul class="wp-block-page-
//     list">`, so each item `<li>` sits one level deeper — a grandchild of the
//     container. A single `container > item` chain therefore styled only the
//     hand-built items and skipped the whole Page List.
// Both chains keep the `>` combinator so the rule stays on top-level items only
// (submenu items live in `__submenu-container`, matched separately below). The
// `css` escape hatch drops all but the last of a comma-separated selector, so
// the two chains can't be comma-combined in one rule — this helper emits one
// rule block per chain instead, with the same trailing selector and body.
const topLevel = ( trailing, declarations ) =>
	'& .wp-block-navigation__container > ' +
	trailing +
	'{' +
	declarations +
	'}' +
	'& .wp-block-navigation__container > .wp-block-page-list > ' +
	trailing +
	'{' +
	declarations +
	'}';

const MAIN_NAVIGATION_CSS =
	// Top-level links only: a buttonish uppercase, semibold, tracked label at the
	// small button font size, plus a reserved 1px transparent bottom border so the
	// current-item underline below adds no height and nothing shifts when it shows.
	// Match the item by its `.wp-block-navigation-item` class, not the `li` tag: on
	// the front end an item is an `<li>`, but in the block editor each item renders
	// as a `<div class="wp-block-navigation-item …">` block-list block, so a
	// `> li >` selector matches nothing in the canvas and the style would look
	// unapplied there. The class is present in both, and the `>` keeps it to
	// top-level items (submenu items sit in `__submenu-container`).
	topLevel(
		'.wp-block-navigation-item > .wp-block-navigation-item__content',
		'text-transform:uppercase;font-weight:600;letter-spacing:0.12rem;' +
			'font-size:var(--wp--preset--font-size--body-xs);' +
			'border-bottom:1px solid transparent'
	) +
	// Current menu item (the page being viewed): reveal that reserved border in the
	// link\'s own colour. `currentColor` keeps it the same black as the link text
	// (and tracks the hover colour with it). WordPress marks the active item\'s
	// `<li>` with `current-menu-item` (front end only — the editor has no "current"
	// item, so it simply won\'t show there). Kept to top-level items via the `>`
	// chain; emits at the same 0-1-0 as the rule above but later in source order,
	// so the colour wins without `!important`. `core/page-list` marks the viewed
	// page's `<li>` with `current-menu-item` too, so the shared helper covers
	// Page List items the same way.
	topLevel(
		'.current-menu-item > .wp-block-navigation-item__content',
		'border-bottom-color:currentColor'
	) +
	// Hover / keyboard-focus colour for every nav link — top-level and dropdown,
	// desktop and the mobile overlay. The colour is set on the link itself (not
	// the `<li>`), which needs `!important`: core pins links to `color:inherit` —
	// `.…-item__content.…-item__content` (0-3-0) on desktop, and in the overlay it
	// also pins the `<li>` with `.is-menu-open … .wp-block-navigation-item`
	// `color:inherit !important` (0-3-0). The variation `css` can't out-specify a
	// 0-3-0 `!important` (WordPress caps it at `:root :where(…)` = 0-1-0), so the
	// `<li>` can't be coloured in the overlay — but the links' own inherit rules
	// are not `!important`, so an `!important` on the link beats them everywhere.
	// No editor control for nav-link colour. Each state is its own rule block:
	// WordPress's `&`-scoping drops all but the last of a comma-separated selector.
	'& .wp-block-navigation-item__content:hover{' +
	'color:var(--wp--preset--color--primary-700) !important' +
	'}' +
	'& .wp-block-navigation-item__content:focus{' +
	'color:var(--wp--preset--color--primary-700) !important' +
	'}' +
	// Slightly larger dropdown chevron on top-level toggles (the `<button>` is a
	// direct child of the top-level item, so this leaves any nested icon alone),
	// nudged up 1px to sit better against the uppercase label. The
	// inner `<svg>` is `width/height:inherit`, so sizing the button is enough.
	// Core sets 0.6em at 0-2-0 (`.wp-block-navigation .…__submenu-icon`), past this
	// `:root :where(…)` rule (0-1-0), so the size needs `!important` (no editor
	// control); the relative nudge has no competing rule. Item matched by class,
	// not `li`, so it also applies to the editor's `<div>` items (see above).
	topLevel(
		'.wp-block-navigation-item > .wp-block-navigation__submenu-icon',
		'width:0.75em !important;height:0.75em !important;' +
			'position:relative;top:-1px'
	) +
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
	// Mobile overlay: green fill instead of core's white. The block's own overlay
	// colour control (`overlayBackgroundColor`) can't do it — it tints the desktop
	// dropdowns and the mobile overlay as one, and those differ here (primary-200
	// vs primary-100) — so it's set here. Core pins the white at
	// `.wp-block-navigation:not(.has-background) .…responsive-container.is-menu-open
	// :not(.disable-default-overlay)` (0-5-0), past this `:root :where(…)` rule, so
	// it needs `!important`.
	'& .wp-block-navigation__responsive-container.is-menu-open{' +
	'background-color:var(--wp--preset--color--primary-100) !important' +
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
