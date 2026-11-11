// Default styling for the Post Navigation Link block (Previous / Next links on
// single posts), matching the Button block's Outline style — the same design
// tokens as the Pagination block (see core-query-pagination.js).
//
// Unlike Pagination, the direction arrow is a <span> sibling of the <a> (not
// part of the link), so the whole block — arrow + title — is styled as one
// outline button box here, rather than just the link. Everything with a
// theme.json key that it CAN express (the box background, border, padding and
// typography) stays structured.
//
// The custom CSS covers only what structured keys can't:
//   * the flex box that keeps the arrow and title on one line (`display`). The
//     arrow↔title gap is left to core's own `margin: 1ch` on the arrow <span>
//     (no `column-gap` here) — that core rule is 0-2-0, so adding our own gap on
//     top, or overriding its margin, would need `!important`; leaning on it keeps
//     the spacing tight and override-free;
//   * the Button's `transition`;
//   * the block-level :hover / :focus-within fill — a plain block, unlike
//     core/button, has no structured :hover key;
//   * the link text colour. This has to follow the *parent block's* hover /
//     focus state (so the whole box inverts no matter where the pointer lands,
//     not just when the <a> itself is hovered) — something `elements.link` can't
//     express, as its pseudo-states only track the link's own state. It also has
//     to sit here for the cascade to resolve: theme.json emits everything at
//     `:root :where(…)` = 0-1-0, so ties break on source order, and WP prints the
//     structured `elements.link` base colour *after* the block's custom CSS —
//     which would otherwise override the hover colour. Declaring the base link
//     colour and the hover/focus colours together, in order, keeps hover last so
//     it wins without `!important`.
//
// Note: WP's process_blocks_custom_css splits the `css` string on `&`, so each
// `&`-rule is written separately with its own braces — never comma-combined
// (`&:hover, &:focus-within { … }` would break the split).
module.exports = {
	color: {
		background: 'transparent',
		// The arrow <span> (not a link) inherits this; flipped to white on
		// hover / focus by the block-level rules in `css` below.
		text: 'var:preset|color|primary-950',
	},
	border: {
		color: 'var:preset|color|primary-950',
		radius: '0',
		style: 'solid',
		width: '1px',
	},
	spacing: {
		padding: {
			top: 'calc(var(--wp--preset--spacing--200) - 1px)',
			bottom: 'calc(var(--wp--preset--spacing--200) - 1px)',
			left: 'calc(var(--wp--preset--spacing--200) - 1px)',
			right: 'calc(var(--wp--preset--spacing--200) - 1px)',
		},
	},
	typography: {
		fontSize: 'var:preset|font-size|body-xs',
		fontWeight: '700',
		letterSpacing: '0.12rem',
		lineHeight: 'var:custom|line-height|display',
		textTransform: 'uppercase',
	},
	css:
		'display: inline-flex; align-items: center; transition: all 0.2s ease-in-out;' +
		' & a { color: var(--wp--preset--color--primary-950); text-decoration: none; }' +
		' &:hover { background-color: var(--wp--preset--color--primary-950); color: var(--wp--preset--color--white); }' +
		' &:focus-within { background-color: var(--wp--preset--color--primary-950); color: var(--wp--preset--color--white); }' +
		' &:hover a { color: var(--wp--preset--color--white); }' +
		' &:focus-within a { color: var(--wp--preset--color--white); }',
};
