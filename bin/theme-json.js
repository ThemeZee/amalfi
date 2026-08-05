/*eslint no-console: 0 */
const path = require( 'path' );
const fs = require( 'fs' );
const fg = require( 'fast-glob' );
const importFresh = require( 'import-fresh' );
const jsonfile = require( 'jsonfile' );
const minimist = require( 'minimist' );

const THEME_JSON_FILE = 'theme.json';

/**
 * Load a configuration fragment provided as JSON or CommonJS module.
 *
 * @param {string} filePath Path to a .json or .js file.
 * @return {Object|Array|undefined} Parsed data, or undefined when unavailable.
 */
const readDataFile = ( filePath = '' ) => {
	try {
		if ( filePath.endsWith( '.js' ) ) {
			const absPath = path.resolve( filePath );
			return importFresh( absPath );
		}
		return jsonfile.readFileSync( filePath );
	} catch ( error ) {
		console.error(
			`Error loading file "${ filePath }": ${ error.message }`
		);
	}
};

/**
 * Expand flattened dotted keys into nested objects and arrays.
 *
 * @param {Record<string, unknown>} obj       Flat key/value pairs.
 * @param {string}                  delimiter Separator used between key segments.
 * @return {Object} Nested representation of the flattened data.
 */
const unflattenObject = ( obj = {}, delimiter = '.' ) => {
	const target = {};

	Object.entries( obj ).forEach( ( [ key, value ] ) => {
		const segments = key.split( delimiter );

		segments.reduce( ( accumulator, segment, index ) => {
			const isLast = index === segments.length - 1;

			if ( isLast ) {
				if (
					typeof accumulator[ segment ] === 'object' &&
					! Array.isArray( accumulator[ segment ] ) &&
					accumulator[ segment ] !== null &&
					typeof value === 'object' &&
					! Array.isArray( value ) &&
					value !== null
				) {
					accumulator[ segment ] = {
						...accumulator[ segment ],
						...value,
					};
				} else {
					accumulator[ segment ] = value;
				}
				return accumulator[ segment ];
			}

			if ( ! accumulator[ segment ] ) {
				const next = segments[ index + 1 ];
				const isArrayIndex = Number.isInteger( Number( next ) );
				accumulator[ segment ] = isArrayIndex ? [] : {};
			}

			return accumulator[ segment ];
		}, target );
	} );

	return target;
};

/**
 * Recursively sort object keys alphabetically.
 * Top-level keys follow a custom order, nested keys are alphabetical.
 *
 * @param {*}       obj              Value to process (object, array, or primitive).
 * @param {string}  parentKey        The parent key of the object.
 * @param {boolean} isTopLevel       Whether this is the root level object.
 * @param {boolean} isCustomVariable Whether this is the settings.custom object.
 * @return {*} Value with sorted keys if object, otherwise unchanged.
 */
const sortObjectKeys = (
	obj,
	parentKey = undefined,
	isTopLevel = true,
	isCustomVariable = false
) => {
	if ( typeof obj !== 'object' || obj === null ) {
		return obj;
	}

	if ( Array.isArray( obj ) ) {
		return obj.map( ( item ) => sortObjectKeys( item, parentKey, false ) );
	}

	const topLevelOrder = [
		'$schema',
		'version',
		'settings',
		'styles',
		'templateParts',
		'customTemplates',
		'patterns',
	];

	let sortedKeys;
	if ( isTopLevel ) {
		const orderedKeys = topLevelOrder.filter( ( key ) => key in obj );
		const remainingKeys = Object.keys( obj )
			.filter( ( key ) => ! topLevelOrder.includes( key ) )
			.sort();
		sortedKeys = [ ...orderedKeys, ...remainingKeys ];
	} else if ( isCustomVariable ) {
		sortedKeys = Object.keys( obj );
	} else {
		sortedKeys = Object.keys( obj ).sort();
	}

	const sorted = {};

	sortedKeys.forEach( ( key ) => {
		sorted[ key ] = sortObjectKeys(
			obj[ key ],
			key,
			false,
			isCustomVariable || ( parentKey === 'settings' && key === 'custom' )
		);
	} );

	return sorted;
};

/**
 * Map a fragment file path to the dotted key used inside theme.json.
 * Supports both folder-based and dot-notation naming:
 * - theme-json/settings/color/palette.json → settings.color.palette
 * - theme-json/settings.color.palette.json → settings.color.palette
 * - theme-json/settings.color/palette.json → settings.color.palette
 * All three approaches produce the same output and can be mixed freely.
 *
 * @param {string} filePath Path pointing to a fragment file.
 * @return {string} Dotted key, or empty string when mapping fails.
 */
const getSettingKey = ( filePath = '' ) => {
	const match = filePath
		.replace( /\\/g, '/' )
		.match( /theme-json\/(.+?)\.(js|json)$/ );

	if ( ! match ) {
		console.warn( `Could not extract relative path from "${ filePath }"` );
		return '';
	}

	const relPath = match[ 1 ]; // e.g. "settings/color-palette"

	const parts = relPath.split( '/' );
	const last = parts.pop();
	if ( last ) {
		parts.push( last.replace( /core-/g, 'core/' ) );
	}

	return parts.join( '.' );
};

/**
 * Assemble the full theme.json by merging fragments from one or more roots.
 *
 * @param {string[]} directories Directories to search for fragment files.
 * @return {Object|undefined} Final theme JSON object, or undefined when nothing found.
 */
const buildThemeJson = ( directories ) => {
	const existingDirectories = directories.filter( ( dir ) => {
		if ( fs.existsSync( dir ) ) {
			return true;
		}

		console.error( `Directory "${ dir }" does not exist.` );
		return false;
	} );

	const toGlobPattern = ( dir ) =>
		`${ dir.replace( /\\/g, '/' ) }/**/*.{json,js}`;

	const patterns = existingDirectories.map( toGlobPattern );

	const dataFiles = patterns.length
		? fg.sync( patterns, { absolute: true, onlyFiles: true } )
		: [];

	if ( ! dataFiles.length ) {
		console.warn( 'No files found to process.' );
		console.warn(
			'Please specify a valid source folder with the --theme-json-dir flag.'
		);
		return;
	}

	let themeJson = {};
	dataFiles.forEach( ( filePath ) => {
		const data = readDataFile( filePath );
		if ( ! data ) {
			return;
		}

		const key = getSettingKey( filePath );
		if ( ! key ) {
			return;
		}
		if ( key === 'index' ) {
			themeJson = { ...themeJson, ...data };
		} else {
			themeJson[ key ] = themeJson[ key ]
				? { ...themeJson[ key ], ...data }
				: data;
		}
	} );
	themeJson = sortObjectKeys( unflattenObject( themeJson ) );

	try {
		jsonfile.writeFileSync( path.resolve( THEME_JSON_FILE ), themeJson, {
			spaces: 4,
		} );
		console.log( 'Theme.json created' );
	} catch ( error ) {
		console.error( `Error writing theme.json: ${ error.message }` );
	}

	return themeJson;
};

/**
 * Collect values for a CLI flag supporting repeated usage.
 *
 * @param {string}   flag                         Flag name, with or without leading dashes.
 * @param {string[]} [argv=process.argv.slice(2)] Argument vector to inspect.
 * @return {string[]} Normalized list of values for the flag.
 */
const getFlagValues = ( flag, argv = process.argv.slice( 2 ) ) => {
	const normalizedFlag = flag.startsWith( '--' ) ? flag : `--${ flag }`;
	const flagName = normalizedFlag.slice( 2 );

	const parsed = minimist( argv, {
		string: [ flagName ],
	} );

	const value = parsed[ flagName ];

	if ( typeof value === 'undefined' ) {
		return [];
	}

	const rawValues = Array.isArray( value ) ? value : [ value ];
	const hasMissingValue = rawValues.some(
		( item ) => item === '' || item === true || typeof item === 'undefined'
	);

	if ( hasMissingValue ) {
		console.warn( `Expected value after "${ normalizedFlag }"` );
		return [];
	}

	return rawValues.filter(
		( item ) => typeof item === 'string' && item.length > 0
	);
};

/**
 * CLI entry point that builds theme.json from provided directories.
 *
 * @param {string[]} [argv=process.argv.slice(2)] Command-line arguments.
 * @return {Object|undefined} Resulting theme JSON structure.
 */
const run = ( argv = process.argv.slice( 2 ) ) => {
	const baseDir = process.cwd();
	const themeJsonDirectory = getFlagValues( '--theme-json-dir', argv ).map(
		( dir ) => path.resolve( baseDir, dir )
	);

	return buildThemeJson( themeJsonDirectory );
};

if ( require.main === module ) {
	run( process.argv.slice( 2 ) );
}

module.exports = {
	readDataFile,
	unflattenObject,
	sortObjectKeys,
	getSettingKey,
	buildThemeJson,
	getFlagValues,
	run,
	THEME_JSON_FILE,
};
