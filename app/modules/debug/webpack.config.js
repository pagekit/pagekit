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
        "vue": "Vue"
    },
    module: {
        loaders: [{ test: /\.vue$/, loader: "vue" }]
    }
}];
