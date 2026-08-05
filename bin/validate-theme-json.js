/*eslint no-console: 0 */
const Ajv = require( 'ajv' );
const addFormats = require( 'ajv-formats' );
const path = require( 'path' );
const RefParser = require( '@apidevtools/json-schema-ref-parser' );

const HTTP_PATTERN = /^https?:\/\//i;
const FILE_URL_PREFIX = 'file://';

/**
 * Resolve a CLI-supplied path into a display-friendly absolute path while
 * leaving remote or file URLs untouched.
 *
 * @param {string} source Path or URL pointing to JSON content.
 * @return {string} Resolved absolute path or the original URL.
 */
const resolveDisplayPath = ( source ) => {
	if ( HTTP_PATTERN.test( source ) || source.startsWith( FILE_URL_PREFIX ) ) {
		return source;
	}

	return path.resolve( source );
};

/**
 * Load and fully dereference a JSON document.
 *
 * @param {string} source Path or URL to the document.
 * @param {string} label  Friendly label for error messages.
 * @return {Promise<{data: unknown, displayPath: string}>} Parsed data and display path.
 */
const loadJsonDocument = async ( source, label ) => {
	try {
		const data = await RefParser.parse( source );
		return { data, displayPath: resolveDisplayPath( source ) };
	} catch ( error ) {
		console.error(
			`Failed to load ${ label } from "${ source }": ${ error.message }`
		);
		throw error;
	}
};

/**
 * Load a JSON Schema, bundling all references into a single document.
 *
 * @param {string} source Path or URL to the schema.
 * @return {Promise<{data: unknown, displayPath: string}>} Bundled schema and display path.
 */
const loadSchema = async ( source ) => {
	try {
		const bundled = await RefParser.bundle( source );
		return { data: bundled, displayPath: resolveDisplayPath( source ) };
	} catch ( error ) {
		console.error(
			`Failed to load schema from "${ source }": ${ error.message }`
		);
		throw error;
	}
};

/**
 * Convert AJV validation errors into human-readable bullet points.
 *
 * @param {import("ajv").ErrorObject[]} [errors=[]] AJV error objects.
 * @return {string} Formatted error summary.
 */
const formatErrors = ( errors = [] ) =>
	errors
		.map( ( error ) => {
			const instancePath = error.instancePath || '/';
			return `- ${ instancePath }: ${ error.message }`;
		} )
		.join( '\n' );

/**
 * CLI entry point for validating a theme JSON against a schema.
 *
 * @param {string[]} [argv=[]] Arguments in the form [themePath, schemaPath].
 * @return {Promise<void>} Promise that resolves when validation completes.
 */
const run = async ( argv = [] ) => {
	const [ themeArg, schemaArg ] = argv;

	if ( ! themeArg || ! schemaArg ) {
		console.error(
			'Usage: node bin/validate-json.js <path-to-theme.json> <path-to-schema>'
		);
		process.exitCode = 1;
		return;
	}

	const themeSource = resolveDisplayPath( themeArg );
	const schemaSource = resolveDisplayPath( schemaArg );

	const [ themeResult, schemaResult ] = await Promise.all( [
		loadJsonDocument( themeSource, 'theme JSON' ),
		loadSchema( schemaSource ),
	] );

	const ajv = new Ajv( {
		allErrors: true,
		strict: false,
	} );
	addFormats( ajv );

	const validate = ajv.compile( schemaResult.data );
	const valid = validate( themeResult.data );

	if ( valid ) {
		console.log(
			`"${ themeResult.displayPath }" is valid against "${ schemaResult.displayPath }"`
		);
		return;
	}

	console.error( 'Validation failed with the following errors:' );
	console.error( formatErrors( validate.errors ) );
	process.exitCode = 1;
};

if ( require.main === module ) {
	run( process.argv.slice( 2 ) ).catch( ( error ) => {
		console.error( `Unexpected validation failure: ${ error.message }` );
		process.exitCode = 1;
	} );
}

module.exports = {
	run,
};
