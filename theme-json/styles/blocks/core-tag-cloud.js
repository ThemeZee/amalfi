// Tag Cloud block styling.
//
// DEFAULT style: the tag links are made to read exactly like the term links in
// the Post Terms block (see core-post-terms.js) — plain uppercase primary-700
// links that go primary-950 on hover, 700 weight, 0.12rem letter-spacing, no
// underline. Kept in lock-step with core-post-terms.js.
//
// Everything there has a structured key except the size: the Tag Cloud assigns
// each tag a per-tag *inline* font-size (from the block's smallest/largest
// range), and an inline style can only be beaten with `!important`. So a single
// `css` rule forces every link to the one Post-Terms size (body-xs), matching
// the uniform look. This deliberately overrides the block's size range — the
// one spot where `!important` is warranted here, by design and at the user's
// request. The structured `typography.fontSize` stays too, so the block and any
// non-link text inherit the same size.
//
// OUTLINE style (`variations.outline`): restyles the block's built-in "Outline"
// block style to match the Button block's Outline style — an outline-button box
// per tag, filled on hover/focus. ("Outline" is a core block.json style, so it
// is not registered in PHP; this only overrides its look.) Like the Pagination
// block (see core-query-pagination.js), a single `elements.link` entry carries
// the box: colors, border, padding and the filled hover/focus state. Core's own
// Outline CSS forces `font-size: unset !important` on each link, so the links
// take their size from this variation's root typography (body-xs), same as the
// Button. The variation's `css` lays the tags out as a wrapping flex row (the
// block has no flex container of its own) — a single `&` rule, so it survives
// WP's split-on-`&`.
module.exports = {
	color: {
		text: 'var:preset|color|primary-700',
	},
	typography: {
		fontSize: 'var:preset|font-size|body-xs',
		fontWeight: '700',
		letterSpacing: '0.12rem',
		textTransform: 'uppercase',
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
	// Override the per-tag inline font-size so every tag is one uniform size,
	// like Post Terms. Inline styles need `!important` to beat.
	css: '& a{font-size:var(--wp--preset--font-size--body-xs) !important}',
	variations: {
		outline: {
			css: '&{display:flex;flex-wrap:wrap;align-items:center;gap:var(--wp--preset--spacing--200)}',
			typography: {
				// Inherited by every tag link (core forces the link font-size
				// to `unset`, so this root size wins — matching the Button).
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
		},
	},
};
