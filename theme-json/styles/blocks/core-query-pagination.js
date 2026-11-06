// Default styling for the Pagination block, matching the Button block's
// Outline style. Everything that has a theme.json key — colors, border,
// padding, typography, blockGap and the hover/focus states — is expressed
// through structured properties here. A single `elements.link` entry styles
// the Previous, Next and page-number links in one place. The only piece that
// needs custom CSS is the current-page marker, which is a <span> (not a link)
// and has no block/element of its own — see core-query-pagination-numbers.js.
module.exports = {
	spacing: {
		// Space between Previous / numbers / Next.
		blockGap: 'var:preset|spacing|200',
	},
	typography: {
		// Inherited by every link and the current-page marker.
		fontSize: 'var:preset|font-size|body-xs',
		fontWeight: '700',
		letterSpacing: '0.12rem',
		lineHeight: 'var:custom|line-height|display',
		textTransform: 'uppercase',
	},
	elements: {
		link: {
			color: {
				background: 'transparent',
				text: 'var:preset|color|primary-950',
			},
			typography: {
				textDecoration: 'none',
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
					left: 'calc(var(--wp--preset--spacing--300) - 1px)',
					right: 'calc(var(--wp--preset--spacing--300) - 1px)',
				},
			},
			':hover': {
				color: {
					background: 'var:preset|color|primary-950',
					text: 'var:preset|color|white',
				},
			},
			':focus': {
				color: {
					background: 'var:preset|color|primary-950',
					text: 'var:preset|color|white',
				},
			},
		},
	},
};
