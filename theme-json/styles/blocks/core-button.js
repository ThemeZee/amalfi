module.exports = {
	variations: {
		outline: {
			border: {
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
				border: {
					color: 'var:preset|color|primary-950',
				},
				color: {
					background: 'var:preset|color|primary-950',
					text: 'var:preset|color|white',
				},
			},
			':active': {
				color: {
					background: 'var:preset|color|primary-950',
					text: 'var:preset|color|white',
				},
			},
		},
	},
};
