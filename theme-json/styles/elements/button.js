module.exports = {
	border: {
		color: 'transparent',
		radius: '0',
		style: 'solid',
		width: '1px',
	},
	color: {
		background: 'var:preset|color|primary-950',
		text: 'var:preset|color|white',
	},
	css: 'transition: all 0.2s ease-in-out',
	spacing: {
		padding: {
			top: 'calc(var(--wp--preset--spacing--200) - 1px)',
			bottom: 'calc(var(--wp--preset--spacing--200) - 1px)',
			left: 'calc(var(--wp--preset--spacing--300) - 1px)',
			right: 'calc(var(--wp--preset--spacing--300) - 1px)',
		},
	},
	typography: {
		fontSize: 'var:preset|font-size|body-xs',
		fontWeight: '700',
		letterSpacing: '0.12rem',
		lineHeight: 'var:custom|line-height|display',
		textTransform: 'uppercase',
	},
	':focus-visible': {
		outline: {
			offset: '3px',
			width: '3px',
			style: 'solid',
			color: 'var:preset|color|primary-950',
		},
	},
	':hover': {
		border: {
			color: 'var:preset|color|primary-950',
		},
		color: {
			background: 'transparent',
			text: 'var:preset|color|primary-950',
		},
	},
	':active': {
		border: {
			color: 'var:preset|color|primary-950',
		},
		color: {
			background: 'transparent',
			text: 'var:preset|color|primary-950',
		},
	},
};
