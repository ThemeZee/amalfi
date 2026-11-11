module.exports = {
	color: {
		text: 'var:preset|color|primary-700',
	},
	elements: {
		link: {
			color: {
				text: 'var:preset|color|primary-700',
			},
			typography: {
				textDecoration: 'none',
			},
			':hover': {
				color: {
					text: 'var:preset|color|primary-950',
				},
			},
		},
	},
	typography: {
		fontSize: 'var:preset|font-size|body-xs',
		fontWeight: '700',
		letterSpacing: '0.12rem',
		textTransform: 'uppercase',
	},
	variations: {
		// Outline — gives each term link the look of the Button block's Outline
		// style: a transparent, 1px-bordered box in the dark primary colour that
		// fills on hover, with a slightly more compact box than a button.
		// Styling only `elements.link` leaves the prefix and suffix with the
		// block's default styling; the comma separator is hidden instead (a plain
		// gap between the bordered boxes reads better than a comma), so the pills
		// carry their own right/bottom margin for spacing.
		outline: {
			// `&` is the block container (`.is-style-outline--N`). The separator
			// is a non-link `<span>` with no design key, so hide it here.
			css: '& .wp-block-post-terms__separator{display:none}',
			elements: {
				link: {
					border: {
						color: 'var:preset|color|primary-950',
						radius: '0',
						style: 'solid',
						width: '1px',
					},
					color: {
						background: 'transparent',
						text: 'var:preset|color|primary-950',
					},
					spacing: {
						margin: {
							right: 'var:preset|spacing|100',
							bottom: 'var:preset|spacing|100',
						},
						padding: {
							top: 'calc(var(--wp--preset--spacing--150) - 1px)',
							bottom: 'calc(var(--wp--preset--spacing--150) - 1px)',
							left: 'calc(var(--wp--preset--spacing--200) - 1px)',
							right: 'calc(var(--wp--preset--spacing--200) - 1px)',
						},
					},
					typography: {
						// Font size inherits the block default (body-xs).
						lineHeight: 'var:custom|line-height|display',
					},
					// `display`/`transition` have no design key; the links render
					// inline, so inline-block is needed for the padded box.
					css: 'display: inline-block; transition: all 0.2s ease-in-out',
					':hover': {
						border: {
							color: 'var:preset|color|primary-950',
						},
						color: {
							background: 'var:preset|color|primary-950',
							text: 'var:preset|color|white',
						},
					},
					':active': {
						border: {
							color: 'var:preset|color|primary-950',
						},
						color: {
							background: 'var:preset|color|primary-950',
							text: 'var:preset|color|white',
						},
					},
					':focus-visible': {
						outline: {
							offset: '3px',
							width: '3px',
							style: 'solid',
							color: 'var:preset|color|primary-950',
						},
					},
				},
			},
		},
	},
};
