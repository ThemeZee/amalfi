// `textInput` element (WordPress 6.9+): styles <textarea> and the text-entry
// <input> types (email, number, password, search, text, tel, url) in one place —
// in any form, whether or not the Form block renders it. `select` shares the same
// look (see ./select.js). Expressed entirely through structured theme.json keys,
// including the keyboard-focus outline: Gutenberg (24.3.0 here, WP 7.1.4) added
// pseudo-state support for form elements, so `:focus-visible` lives here too — no
// custom CSS needed for the fields at all.
//
// Primary-200 fill, no border, 8px radius, comfortable padding, body typography,
// and a primary-500 focus ring for keyboard users (the fill replaces the border).
module.exports = {
	border: {
		radius: '8px',
		style: 'none',
		width: '0',
	},
	color: {
		background: 'var:preset|color|primary-200',
		text: 'var:preset|color|primary-950',
	},
	':focus-visible': {
		outline: {
			color: 'var:preset|color|primary-500',
			offset: '2px',
			style: 'solid',
			width: '2px',
		},
	},
	spacing: {
		padding: {
			top: 'var:preset|spacing|150',
			bottom: 'var:preset|spacing|150',
			left: 'var:preset|spacing|200',
			right: 'var:preset|spacing|200',
		},
	},
	typography: {
		fontFamily: 'var:custom|font-family|body',
		fontSize: 'var:preset|font-size|body',
		lineHeight: 'var:custom|line-height|body',
	},
};
