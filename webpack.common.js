const path = require('path')

module.exports = {
  entry: {
    app: `./js/app.js`
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, `dist`)
  },
  module: {
    rules: [
      {
        test: /\.js?$/,
        exclude: [/node_modules/],
        loader: 'babel-loader'
      },
      {
        test: require.resolve('jquery'),
        loader: 'expose-loader',
        options: {
          exposes: ['$', 'jQuery']
        }
      }
    ]
  },
  resolve: {
    modules: ['node_modules'],
    extensions: ['.js']
  }
}
