module.exports = {
	variations: {
		// Overline — small, uppercase, letter-spaced label used above headings.
		// Pure typography and colour, so it is expressed entirely with design
		// keys (no `css` property needed).
		overline: {
			color: {
				text: 'var:preset|color|primary-600',
			},
			typography: {
				fontSize: 'var:preset|font-size|body-xs',
				fontWeight: '700',
				letterSpacing: '0.12rem',
				textTransform: 'uppercase',
			},
		},
	},
};
