// Quote — themed to match Amalfi. By default the quote is framed like the
// testimonial cards: a full 1px rule in primary-200 with 8px rounded corners
// and even padding all round. The body text keeps the body-lg italic used by
// those cards, and the citation is turned into the same small uppercase eyebrow
// as the Overline style (primary-600, Manrope). The citation is an inline
// `<cite>` — neither a block nor a theme.json element — so it is styled through
// the `css` escape hatch.
//
// The base here applies to every quote (`.wp-block-quote`), so the typography
// and citation treatment are shared. The core-registered `plain` block style
// drops the frame for a cleaner look: a single left accent rule in primary-300
// (the theme's former default) with just a left indent.
module.exports = {
	color: {
		text: 'var:preset|color|primary-800',
	},
	border: {
		color: 'var:preset|color|primary-200',
		radius: '8px',
		style: 'solid',
		width: '1px',
	},
	spacing: {
		padding: {
			top: 'var:preset|spacing|500',
			right: 'var:preset|spacing|500',
			bottom: 'var:preset|spacing|500',
			left: 'var:preset|spacing|500',
		},
	},
	typography: {
		fontSize: 'var:preset|font-size|body-lg',
		fontStyle: 'italic',
	},
	css:
		'& cite{display:block;margin-top:var(--wp--preset--spacing--200);' +
		'font-family:var(--wp--preset--font-family--manrope);' +
		'font-size:var(--wp--preset--font-size--body-xs);font-style:normal;' +
		'font-weight:700;letter-spacing:0.12rem;text-transform:uppercase;' +
		'color:var(--wp--preset--color--primary-600)}',
	variations: {
		// Plain — drops the surrounding frame and rounded corners in favour of a
		// single left accent rule in primary-300 with a left indent (the theme's
		// former default quote styling), keeping the shared italic body text.
		plain: {
			border: {
				radius: '0',
				top: {
					style: 'none',
					width: '0',
				},
				right: {
					style: 'none',
					width: '0',
				},
				bottom: {
					style: 'none',
					width: '0',
				},
				left: {
					color: 'var:preset|color|primary-300',
					style: 'solid',
					width: '4px',
				},
			},
			spacing: {
				padding: {
					top: '0',
					right: '0',
					bottom: '0',
					left: 'var:preset|spacing|400',
				},
			},
		},
	},
};
