// Accordion Heading — the small, uppercase, letter-spaced label of the Overline
// block style (see core-heading.js / core-paragraph.js), coloured like the
// links in the Main Navigation block style (see core-navigation.js): the near-
// black base text colour (primary-950) by default, shifting to primary-700 on
// hover, with no underline.
//
// The heading renders as an `<h3>`, so like the Heading Overline it inherits the
// display (serif) font, italic style, weight 600 and tight display line-height
// from `styles.elements.heading`; the typography here resets those back to the
// body values. The toggle `<button>` and its title `<span>` both inherit these
// values (the button declares `font: inherit`), so styling the block root reaches
// the visible label.
//
// Everything that a design key can't express lives in `css`:
//   - the hover / keyboard-focus colour shift, matching the nav links (a plain
//     block can't take a `:hover` design key — VALID_BLOCK_PSEUDO_SELECTORS only
//     allows it for the link/button elements and core/button, core/navigation-
//     link). Both the base colour (`color.text`, a design key emitted first) and
//     these `css` rules sit at `:root :where(…)` = 0-1-0, so the later `css`
//     wins the hover without `!important`;
//   - suppressing core's hover underline on the title. Core sets it at
//     `.…__toggle:hover .…__toggle-title` (0-4-0), past this 0-1-0 rule, so it
//     needs `!important` (there is no editor control for it);
//   - restoring the toggle icon to its previous size. The overline shrinks the
//     label to body-xs, and the `+`/`×` icon (a 1.2em box around a text glyph)
//     shrank with it, so its font size is pinned back to body-xl — the heading's
//     former size — leaving the label small;
//   - zeroing the toggle's own `1em 0` vertical padding so the Accordion Item's
//     even padding is the sole, symmetric inset from the card border.
module.exports = {
	color: {
		text: 'var:preset|color|primary-950',
	},
	typography: {
		fontFamily: 'var:custom|font-family|body',
		fontSize: 'var:preset|font-size|body-xs',
		fontStyle: 'normal',
		fontWeight: '700',
		letterSpacing: '0.12rem',
		lineHeight: 'var:custom|line-height|body',
		textTransform: 'uppercase',
	},
	css:
		// Hover / keyboard-focus colour, set on the toggle so the inheriting
		// title span follows. Each state is its own rule block: WordPress's
		// `&`-scoping keeps only the last selector of a comma-separated list.
		'& .wp-block-accordion-heading__toggle:hover{' +
		'color:var(--wp--preset--color--primary-700)' +
		'}' +
		'& .wp-block-accordion-heading__toggle:focus-visible{' +
		'color:var(--wp--preset--color--primary-700)' +
		'}' +
		// Drop core's hover underline on the title (0-4-0, so `!important`).
		'& .wp-block-accordion-heading__toggle:hover ' +
		'.wp-block-accordion-heading__toggle-title{text-decoration:none !important}' +
		// Keep the toggle icon at its former (pre-overline) size.
		'& .wp-block-accordion-heading__toggle-icon{' +
		'font-size:var(--wp--preset--font-size--body-xl)' +
		'}' +
		// Reset the toggle's built-in vertical padding so the item padding is the
		// only inset; horizontal toggle padding is already 0.
		'& .wp-block-accordion-heading__toggle{padding-top:0;padding-bottom:0}',
};
