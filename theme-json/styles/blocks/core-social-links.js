module.exports = {
	variations: {
		// Outline — mirrors the Outline style of the Button block. Everything is
		// expressed in theme.json on the inner social-link block: the resting
		// look through design keys (transparent, 1px-bordered circle in the dark
		// primary colour; the icon SVG inherits the link's `color` via its
		// `fill: currentColor`), and the rest — which has no dedicated theme.json
		// key — through the `css` property. WordPress scopes all of it to a
		// generated `.is-style-outline--N` class at render time.
		outline: {
			blocks: {
				'core/social-link': {
					border: {
						color: 'var:preset|color|primary-950',
						style: 'solid',
						width: '1px',
					},
					color: {
						background: 'transparent',
						text: 'var:preset|color|primary-950',
					},
					// `&` is the social-link selector (the `<li>`). These rules
					// cover what design keys cannot express:
					// - the hover/focus fill (pseudo-class states are not valid
					//   theme.json keys for this block);
					// - suppressing the hover scale-up core applies. Everything the
					//   `css` property emits is wrapped in `:root :where(…)`
					//   (specificity 0-1-0), which cannot beat core's
					//   `.wp-block-social-link:hover` (0-2-0) on its own, so this
					//   single declaration uses `!important`. It is safe here: there
					//   is no editor control for `transform`, so it overrides no user
					//   setting.
					// - the smooth transition (no `transition` key);
					// - the roomier icon padding, set on the inner anchor (design
					//   keys can only reach the social-link block, not its anchor);
					// - a reset of the border the editor otherwise doubles, because
					//   there the inner anchor button also carries the
					//   `wp-block-social-link` class the border is scoped to.
					// `:not(.wp-block-social-link-anchor)` keeps the per-icon rules
					// off that inner anchor button.
					css:
						'&:not(.wp-block-social-link-anchor){transition:all 0.2s ease-in-out}' +
						'& .wp-block-social-link-anchor{padding:0.35em}' +
						'&.wp-block-social-link-anchor{border:0}' +
						'&:not(.wp-block-social-link-anchor):hover{background-color:var(--wp--preset--color--primary-950);color:var(--wp--preset--color--white);transform:none !important}' +
						'&:not(.wp-block-social-link-anchor):focus-within{background-color:var(--wp--preset--color--primary-950);color:var(--wp--preset--color--white)}',
				},
			},
		},
	},
};
