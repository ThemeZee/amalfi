module.exports = {
	variations: {
		// Overline — small, uppercase, letter-spaced label, matching the
		// Paragraph Overline style (see core-paragraph.js) pixel for pixel.
		//
		// The Paragraph version is pure typography/colour because a paragraph
		// already inherits the body font, upright style and body line-height.
		// A heading does not: `styles.elements.heading` gives every heading the
		// display (serif) font, italic style, weight 600 and the tight display
		// line-height. So this variation additionally resets those heading
		// defaults back to the body values, leaving the two styles identical.
		overline: {
			color: {
				text: 'var:preset|color|primary-600',
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
		},
	},
};
