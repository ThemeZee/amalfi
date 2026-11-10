// Caption — the shared treatment for every block caption (Image, Gallery, Table,
// Audio, Video, Embed …). WordPress tags each caption with `wp-element-caption`,
// so this single element entry styles them all at once: a small body-xs size and
// a lighter primary-700 green that sits back from the near-black body text while
// still clearing WCAG AA for small text (~5.7:1 on the primary-100 page).
module.exports = {
	color: {
		text: 'var:preset|color|primary-700',
	},
	typography: {
		fontSize: 'var:preset|font-size|body-xs',
	},
};
