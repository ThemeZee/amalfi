const PRIMARY = 'var(--wp--preset--color--primary-950)';
const WHITE = 'var(--wp--preset--color--white)';

// Shared `css` for both Social Icons variations — the parts no design key can
// express. `&` is the social-link selector (the `<li>`):
// - the smooth transition (no `transition` key);
// - the roomier icon padding, set on the inner anchor (design keys can only
//   reach the social-link block, not its anchor), matched across variations so
//   icons stay the same size;
// - resetting the design-key border/background/colour that the editor otherwise
//   applies twice: there the inner anchor button also carries the
//   `wp-block-social-link` class the design keys are scoped to, so it picks up
//   its own border/fill. Neutralising them makes the button inherit the list
//   item — the fill comes from the `<li>` in every state, as on the frontend
//   (whose anchor has no such class), instead of a doubled border or a button
//   that stays filled when the `<li>` flips on hover.
// `:not(.wp-block-social-link-anchor)` keeps the per-icon rules off that anchor.
const ICON_BASE =
	'&:not(.wp-block-social-link-anchor){transition:all 0.2s ease-in-out}' +
	'& .wp-block-social-link-anchor{padding:0.35em}' +
	'&.wp-block-social-link-anchor{border:0;background-color:transparent;color:inherit}';

// Hover/focus fill (pseudo-class states are not valid theme.json keys for this
// block). On hover it also suppresses the scale-up core applies via
// `.wp-block-social-link:hover` (0-2-0): the `css` property emits at
// `:root :where(…)` (0-1-0), which only beats that with `!important`. That is
// safe here — there is no editor control for `transform`, so it overrides no
// user setting. (Core applies no scale on focus, so focus needs no reset.)
const fill = ( background, text ) =>
	`&:not(.wp-block-social-link-anchor):hover{background-color:${ background };color:${ text };transform:none !important}` +
	`&:not(.wp-block-social-link-anchor):focus-within{background-color:${ background };color:${ text }}`;

module.exports = {
	variations: {
		// Outline — mirrors the Outline style of the Button block: a transparent,
		// 1px-bordered circle in the dark primary colour that fills on hover.
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
					css: ICON_BASE + fill( PRIMARY, WHITE ),
				},
			},
		},

		// Primary — mirrors the default (filled) style of the Button block: the
		// inverse of Outline. A solid primary-950 circle with white icons at
		// rest that flips to the Outline look on hover. Same box model as Outline
		// (1px border + 0.35em padding) so the icons are the same size. The icon
		// SVG inherits the link's `color` via its `fill: currentColor`.
		primary: {
			blocks: {
				'core/social-link': {
					border: {
						color: 'var:preset|color|primary-950',
						style: 'solid',
						width: '1px',
					},
					color: {
						background: 'var:preset|color|primary-950',
						text: 'var:preset|color|white',
					},
					css: ICON_BASE + fill( 'transparent', PRIMARY ),
				},
			},
		},
	},
};
