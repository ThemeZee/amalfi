/**
 * Jest configuration for the TypeScript admin app.
 *
 * Extends the @wordpress/scripts unit-test config (jsdom, Babel transform for
 * TS/TSX, SCSS mocked to an empty module) and adds the Testing Library jest-dom
 * matchers. Test files live under `tests/ts/` as `*.test.tsx`.
 */
const defaultConfig = require( '@wordpress/scripts/config/jest-unit.config' );

module.exports = {
	...defaultConfig,
	rootDir: '.',
	testMatch: [ '<rootDir>/tests/ts/**/*.test.[jt]s?(x)' ],
	moduleNameMapper: {
		...( defaultConfig.moduleNameMapper || {} ),
		// The real @wordpress/components is a runtime external; use a light
		// DOM-only stand-in so jsdom tests stay fast and deterministic.
		'^@wordpress/components$': '<rootDir>/tests/ts/mocks/wp-components.tsx',
	},
	setupFilesAfterEnv: [
		...( defaultConfig.setupFilesAfterEnv || [] ),
		'<rootDir>/tests/ts/setup.ts',
	],
};
