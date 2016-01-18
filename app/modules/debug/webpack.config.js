module.exports = [{
    entry: {
        "debugbar": "./app/debugbar"
    },
    output: {
        filename: "./app/bundle/[name].js",
        library: "Debugbar"
    },
    externals: {
        "jquery": "jQuery",
        "lodash": "_",
        "vue": "Vue"
    },
    module: {
        loaders: [{ test: /\.vue$/, loader: "vue" }]
    }
}];
