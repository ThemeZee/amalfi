// Table — themed with the primary palette: a primary-200 header fill, thin
// primary-200 cell rules and the body font at body-sm for a denser, legible
// grid. Cells and header are sub-nodes with no theme.json keys of their own, so
// they go through `css`. The caption is styled globally via the shared
// `styles.elements.caption` entry (see theme-json/styles/elements/caption.js),
// not here.
//
// The base styles every table (`.wp-block-table`); the `stripes` variation
// drops the cell rules and header fill in favour of alternating primary-200 rows
// with a single underline beneath the header.
//
// Header cells are left-aligned explicitly on `& thead th`: the `& th,& td`
// shorthand sets `text-align:left` on the body cells, but WordPress drops the
// `th` half of that comma selector when it compiles the `css` hatch, so the
// header would otherwise fall back to the browser default (centre).
//
// The stripe row fill is NOT set here. Core ships a grey default
// (`.wp-block-table.is-style-stripes tbody tr:nth-child(2n+1)`, 0-3-2), and this
// `css` hatch can only emit a 0-1-0 `:where()` rule, which loses to it. The
// stripe colour therefore lives in `resources/scss/global/_block-styles.scss`
// at matched specificity, where it beats core's default by load order while
// still yielding to a user-chosen stripe background — see that file.
module.exports = {
	color: {
		text: 'var:preset|color|primary-950',
	},
	typography: {
		fontSize: 'var:preset|font-size|body-sm',
	},
	css:
		'& table{width:100%;border-collapse:collapse}' +
		'& th,& td{border:1px solid var(--wp--preset--color--primary-200);' +
		'padding:var(--wp--preset--spacing--150) var(--wp--preset--spacing--300);' +
		'text-align:left}' +
		'& thead th{background-color:var(--wp--preset--color--primary-200);' +
		'font-weight:700;letter-spacing:0.02rem;text-align:left}' +
		'& tfoot td{font-weight:700}',
	variations: {
		stripes: {
			css:
				'& th,& td{border-color:transparent}' +
				'& thead th{background-color:transparent;' +
				'border-bottom:1px solid var(--wp--preset--color--primary-300)}',
		},
	},
};
