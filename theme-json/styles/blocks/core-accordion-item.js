// Accordion Item — each item is a self-contained card with a soft primary-200
// fill and 8px rounded corners (no border). The accordion keeps the default
// block gap between items, so the rounded, filled blocks read as separate cards
// rather than one divided list.
//
// Padding is kept tight and even on all four sides. The heading toggle's own
// vertical padding is reset to 0 (see core-accordion-heading.js) so this single
// value is the whole inset from the card edge, top/bottom and left/right alike.
module.exports = {
	color: {
		background: 'var:preset|color|primary-200',
	},
	border: {
		radius: '8px',
	},
	spacing: {
		padding: {
			top: 'var:preset|spacing|200',
			right: 'var:preset|spacing|200',
			bottom: 'var:preset|spacing|200',
			left: 'var:preset|spacing|200',
		},
	},
};
