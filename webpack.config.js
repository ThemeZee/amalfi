const config = require( '@wordpress/scripts/config/webpack.config' );

module.exports = {
	...config,
	entry: {
		editor: './resources/scss/editor/index.scss',
		frontend: './resources/scss/frontend/index.scss',
	},
	output: {
		publicPath: './',
		path: __dirname + '/assets',
		filename: '[name].js',
	},
};
