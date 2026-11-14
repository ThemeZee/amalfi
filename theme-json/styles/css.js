// Top-level custom CSS, emitted raw into the global styles stylesheet (no
// wrapper/prefix), so these selectors apply site-wide. Reserved for the few
// things theme.json has no structured key for. All of the form-field styling
// (fill, radius, padding, typography, focus outline) lives in the structured
// `textInput`/`select` elements — theme-json/styles/elements/.

// Linked headings flip the normal link colors: default heading/text color,
// dark green on hover, and never underlined — matching the post title styling.
// `a:not(.wp-element-button)` is specific enough to override the global link
// element (which uses :where(), so it carries no specificity) without !important.
const linkedHeadings =
	':where(h1, h2, h3, h4, h5, h6) a:not(.wp-element-button){color:var(--wp--preset--color--primary-950);text-decoration:none}' +
	':where(h1, h2, h3, h4, h5, h6) a:not(.wp-element-button):hover{color:var(--wp--preset--color--primary-700);text-decoration:none}';

// Submit/reset/button inputs aren't covered by theme.json even in WP 7.1 /
// Gutenberg 24.3.0: the `button` element targets the `.wp-element-button` class,
// not a raw `input[type=submit]` tag (class-wp-theme-json-gutenberg.php maps
// 'button' => '.wp-element-button, .wp-block-button__link'). So mirror the Core
// Button element here — same preset values as theme-json/styles/elements/button.js.
// Anything already tagged `.wp-element-button` keeps Core's canonical button styling.
const submitButtons =
	'input[type=submit]:not(.wp-element-button),' +
	'input[type=reset]:not(.wp-element-button),' +
	'input[type=button]:not(.wp-element-button)' +
	'{' +
	'background-color:var(--wp--preset--color--primary-950);' +
	'color:var(--wp--preset--color--white);' +
	'border:1px solid transparent;' +
	'border-radius:0;' +
	'padding:calc(var(--wp--preset--spacing--200) - 1px) calc(var(--wp--preset--spacing--300) - 1px);' +
	'font-size:var(--wp--preset--font-size--body-xs);' +
	'font-weight:700;' +
	'letter-spacing:0.12rem;' +
	'line-height:var(--wp--custom--line-height--display);' +
	'text-transform:uppercase;' +
	'cursor:pointer;' +
	'transition:all 0.2s ease-in-out;' +
	'}' +
	'input[type=submit]:not(.wp-element-button):hover,' +
	'input[type=reset]:not(.wp-element-button):hover,' +
	'input[type=button]:not(.wp-element-button):hover,' +
	'input[type=submit]:not(.wp-element-button):active,' +
	'input[type=reset]:not(.wp-element-button):active,' +
	'input[type=button]:not(.wp-element-button):active' +
	'{' +
	'background-color:transparent;' +
	'color:var(--wp--preset--color--primary-950);' +
	'border-color:var(--wp--preset--color--primary-950);' +
	'}' +
	'input[type=submit]:not(.wp-element-button):focus-visible,' +
	'input[type=reset]:not(.wp-element-button):focus-visible,' +
	'input[type=button]:not(.wp-element-button):focus-visible' +
	'{' +
	'outline:3px solid var(--wp--preset--color--primary-950);' +
	'outline-offset:3px;' +
	'}';

module.exports = linkedHeadings + submitButtons;
