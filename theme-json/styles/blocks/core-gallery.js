// Gallery — fixes the rounded corners on captioned gallery images.
//
// Each gallery item is a core/image whose `img` carries the theme's 8px radius
// (see core-image.js). When the image has a caption, core renders that caption
// as an absolutely-positioned gradient overlay pinned to the bottom of the image
// figure — and the overlay has square corners, so it covered the image's rounded
// bottom corners and made captioned items look squared off.
//
// Rounding the image figure itself and clipping its overflow confines the
// overlay to the rounded shape, so the corners stay rounded. `overflow` has no
// theme.json key and this targets the nested image figure (not the gallery
// root), so it goes through the `css` escape hatch. `&` is the gallery root.
module.exports = {
	css: '& figure.wp-block-image{border-radius:8px;overflow:hidden}',
};
