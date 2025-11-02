/**
 * Copyright since 2007 PrestaShop SA ...
 *
 * PrestaShop Starter Theme Webpack Configuration
 */
const webpack = require('webpack');
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const CssoWebpackPlugin = require('csso-webpack-plugin').default;
const LicensePlugin = require('webpack-license-plugin');

const config = {
  entry: {
    theme: ['./js/theme.js', './css/theme.scss'],
    error: ['./css/error.scss']
  },
  output: {
    path: path.resolve(__dirname, '../assets/js'),
    filename: '[name].js'
  },
  resolve: {
    preferRelative: true
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        use: 'esbuild-loader'
      },
      {
        test: /\.scss$/,
        use: [
          // MiniCssExtractPlugin.loader wyciąga CSS do osobnych plików
          MiniCssExtractPlugin.loader,
          'css-loader',
          'postcss-loader',
          {
            loader: 'sass-loader',
            options: {
              // Wymuszenie użycia Dart Sass
              implementation: require('sass'),
              sassOptions: {
                // Wyszukujemy importy również w folderze node_modules
                includePaths: [path.resolve(__dirname, 'node_modules')]
              },
              sourceMap: true
            }
          }
        ]
      },
      {
        test: /\.(png|woff(2)?|eot|otf|ttf|svg|gif)(\\?[a-z0-9=\\.]+)?$/,
        type: 'asset/resource',
        generator: {
          filename: '../css/[hash][ext]'
        }
      },
      {
        test: /\.css$/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {
              sourceMap: true
            }
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true
            }
          }
        ]
      }
    ]
  },
  devtool: 'source-map',
  externals: {
    prestashop: 'prestashop',
    $: '$',
    jquery: 'jQuery'
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: path.join('..', 'css', '[name].css')
    }),
    new CssoWebpackPlugin({
      forceMediaMerge: true
    }),
    new LicensePlugin({
      outputFilename: 'thirdPartyNotice.json',
      licenseOverrides: {
        'bootstrap-touchspin@3.1.1': 'Apache-2.0'
      },
      replenishDefaultLicenseTexts: true
    })
  ]
};

if (process.env.NODE_ENV === 'production') {
  config.optimization = {
    minimize: true,
    minimizer: [
      new TerserPlugin({
        parallel: true,
        extractComments: false
      })
    ]
  };
}

module.exports = config;
