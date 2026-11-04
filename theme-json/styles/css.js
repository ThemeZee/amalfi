// Linked headings flip the normal link colors: default heading/text color,
// dark green on hover, and never underlined — matching the post title styling.
// `a:not(.wp-element-button)` is specific enough to override the global link
// element (which uses :where(), so it carries no specificity) without !important.
module.exports =
	':where(h1, h2, h3, h4, h5, h6) a:not(.wp-element-button){color:var(--wp--preset--color--primary-950);text-decoration:none}' +
	':where(h1, h2, h3, h4, h5, h6) a:not(.wp-element-button):hover{color:var(--wp--preset--color--primary-700);text-decoration:none}';
