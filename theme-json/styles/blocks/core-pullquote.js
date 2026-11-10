// Pullquote — a feature element, so it leans on the display serif (Cormorant
// Garamond, italic) at the display-sm size. Rather than the open top/bottom
// rules it used to carry, it now sits on a soft primary-200 panel (a touch
// darker than the page) with rounded corners and generous left/right padding,
// so the quote reads as a self-contained feature. The citation reuses the
// Overline eyebrow treatment (primary-600, Manrope); `<cite>` is an inline node
// with no block/element key, so it is set through `css`.
module.exports = {
	color: {
		background: 'var:preset|color|primary-200',
		text: 'var:preset|color|primary-900',
	},
	border: {
		radius: '8px',
	},
	spacing: {
		padding: {
			top: 'var:preset|spacing|500',
			right: 'var:preset|spacing|800',
			bottom: 'var:preset|spacing|500',
			left: 'var:preset|spacing|800',
		},
	},
	typography: {
		fontFamily: 'var:custom|font-family|display',
		fontSize: 'var:preset|font-size|display-sm',
		fontStyle: 'italic',
		lineHeight: 'var:custom|line-height|display',
	},
	css:
		'& cite{display:block;margin-top:var(--wp--preset--spacing--300);' +
		'font-family:var(--wp--preset--font-family--manrope);' +
		'font-size:var(--wp--preset--font-size--body-xs);font-style:normal;' +
		'font-weight:700;letter-spacing:0.12rem;text-transform:uppercase;' +
		'color:var(--wp--preset--color--primary-600)}',
};
