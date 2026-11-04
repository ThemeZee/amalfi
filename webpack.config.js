const CopyWebpackPlugin = require( 'copy-webpack-plugin' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

const config = {
	...defaultConfig,
	plugins: [
		...defaultConfig.plugins,
		new CopyWebpackPlugin( {
			patterns: [
				{
					from: 'resources/fonts/**/*.(woff|woff2)',
					to: 'fonts/[name][ext]',
					force: true,
				},
				{
					from: 'resources/images/**/*.(jpg|jpeg|png|gif|webp|svg)',
					to: 'images/[name][ext]',
					force: true,
				},
			],
		} ),
	],
};

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
