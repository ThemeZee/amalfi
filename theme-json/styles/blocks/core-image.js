// Image — rounded by default with the theme's 8px corner radius, matching the
// images used across the home page. This also rounds the images inside a
// Gallery, since each gallery item is a core/image. The core "Rounded" block
// style (a near-circle) still wins where it is applied, as its own rule carries
// higher specificity.
module.exports = {
	border: {
		radius: '8px',
	},
};
