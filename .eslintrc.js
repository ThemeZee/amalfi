/**
 * ESLint configuration.
 *
 * Extends the WordPress coding standards via @wordpress/eslint-plugin (bundled
 * with @wordpress/scripts). Because `typescript` is installed, the recommended
 * config automatically enables the @typescript-eslint parser and rules for
 * `.ts`/`.tsx` files and ignores `.d.ts` declaration files.
 */
module.exports = {
	root: true,
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended' ],
	env: {
		browser: true,
	},
	overrides: [
		{
			// Jest globals and testing-library best practices for the suite.
			files: [ 'tests/ts/**/*.[jt]s?(x)' ],
			extends: [ 'plugin:@wordpress/eslint-plugin/test-unit' ],
		},
	],
};
