// Media & Text — rounds the media side with the theme's 8px radius. The media
// is a raw `<figure>`/`<img>`/`<video>` inside the block (not a core/image), so
// it can't be reached with the image block's own rounding; rounding the media
// cell and clipping it with `overflow:hidden` rounds the image, video or
// background-fill alike. This needs the `css` escape hatch because the media
// cell is a sub-node with no theme.json key of its own.
module.exports = {
	css: '& .wp-block-media-text__media{border-radius:8px;overflow:hidden}',
};
