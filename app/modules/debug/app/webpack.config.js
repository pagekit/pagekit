module.exports = [{
  entry    : {
    "debugbar": __dirname + "/debugbar"
  },
  output   : {
    filename: __dirname + "/bundle/[name].js"
  },
  externals: {
    "jquery": "jQuery",
    "vue"   : "Vue"
  },
  module   : {
    loaders: [{
      test  : /\.vue$/,
      loader: "vue"
    }]
  }
}];
