// Code — a soft primary-200 card with the theme's 8px corner rounding (the same
// radius as the home-page images/cards). The tint sits a shade darker than the
// page, so the snippet reads as a distinct panel without needing a border. The
// monospaced font stack has no theme.json key, so it is set through `css` on
// the block root (inherited by the inner `<code>`).
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
	css:
		'font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,' +
		'"Liberation Mono",monospace;overflow:auto',
};
