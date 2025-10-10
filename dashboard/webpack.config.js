const defaults = require('@wordpress/scripts/config/webpack.config');

module.exports = {
  ...defaults,
  module: {
    ...defaults.module,
    rules: [
      ...defaults.module.rules,
      {
        test: /\.(js|jsx)$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env', '@babel/preset-react']
          }
        }
      }
    ]
  },
  resolve: {
    ...defaults.resolve,
    extensions: ['.js', '.jsx']
  }
};