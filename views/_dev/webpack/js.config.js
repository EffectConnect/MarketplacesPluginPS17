const webpack = require('webpack');
const dev = process.argv.indexOf('--env=dev') !== -1; // In production mode ?
const path = require('path');

let plugins = [];

let js_config = {
  entry: {
    adminconnectioncontroller: [
      './js/adminconnectioncontroller.js'
    ]
  },
  output: {
    path: path.resolve(__dirname, '../../assets/js'),
    filename: '[name].js'
  },
  /* suppress node shims */
	node: {
		process: false,
		Buffer: false
	},
  module: {
    rules: [
      {
         use: {
            loader:'babel-loader',
            options: { presets: ['es2015'] }
         },
         test: /\.js$/,
         exclude: /node_modules/
      }
    ]
  },
  externals: {
    prestashop: 'prestashop'
  },
  plugins: plugins,
  resolve: {
    extensions: ['.js']
  }
};

js_config.plugins = js_config.plugins||[];
if (dev) {
  js_config.devtool = 'source-map';
  js_config.cache = true;
} else {
  js_config.cache = false;
  js_config.plugins.push(
    new webpack.optimize.UglifyJsPlugin({
      sourceMap: false,
      compress: {
        sequences: true,
        conditionals: true,
        booleans: true,
        if_return: true,
        join_vars: true,
        drop_console: true
      },
      output: {
        comments: false
      },
      minimize: true
    })
  );
}

module.exports = js_config;

