// The page numbers render as a bare run of inline <a>/<span> elements with no
// layout container, and the current page is a <span class="page-numbers
// current"> that is not a link. Neither can be reached through structured
// theme.json keys, so the little that is left lives here as CSS:
//   1. lay the numbers out in a row with spacing (also makes the links flex
//      items so their structured padding renders as proper boxes), and
//   2. give the current-page marker the filled Outline-button active look.
// The resting box style, colors, typography and hover/focus states come from
// the structured `elements.link` entry in core-query-pagination.js.
module.exports = {
	css: [
		'&{display:flex;flex-wrap:wrap;align-items:center;gap:var(--wp--preset--spacing--200)}',
		'& .page-numbers.current{',
		'border:1px solid var(--wp--preset--color--primary-950);',
		'border-radius:0;',
		'padding:calc(var(--wp--preset--spacing--200) - 1px) calc(var(--wp--preset--spacing--300) - 1px);',
		'background-color:var(--wp--preset--color--primary-950);',
		'color:var(--wp--preset--color--white)}',
	].join( '' ),
};
