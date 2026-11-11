// Default styling for the Post Navigation Link block (Previous / Next links on
// single posts). Styled to read the same as the Tags links in the post footer
// (core/post-terms, body-xs / 12px) — plain uppercase links, not a
// button. Kept in lock-step with core-post-terms.js: primary-700 links that go
// primary-950 on hover, no underline, 700 weight, 0.12rem letter-spacing,
// uppercase. Pure structured keys — no border/background/box, so no css escape
// hatch is needed (the link's own :hover is enough; WP emits element :hover
// after the base rule, so it wins without the ordering tricks a box invert needs).
module.exports = {
	color: {
		// The direction arrow <span> (a sibling of the link, not a link itself)
		// inherits this.
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
};
