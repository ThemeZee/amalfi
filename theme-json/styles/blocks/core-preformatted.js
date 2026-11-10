// Preformatted — the same soft primary-200 card as the Code block (8px rounding,
// no border) sitting a shade darker than the page, but it keeps the body font
// rather than a monospace stack, since Preformatted is for preserving whitespace
// in ordinary text. `overflow:auto` (no theme.json key) guards against long
// lines.
module.exports = {
	color: {
		background: 'var:preset|color|primary-200',
		text: 'var:preset|color|primary-950',
	},
	border: {
		radius: '8px',
	},
	spacing: {
		padding: {
			top: 'var:preset|spacing|200',
			right: 'var:preset|spacing|300',
			bottom: 'var:preset|spacing|200',
			left: 'var:preset|spacing|300',
		},
	},
	typography: {
		fontSize: 'var:preset|font-size|body-sm',
	},
	css: 'overflow:auto',
};
