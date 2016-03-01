module.exports = [{
    entry: {
        "debugbar": "./app/debugbar"
    },
    output: {
        filename: "./app/bundle/[name].js",
        library: "Debugbar"
    },
    module: {
        loaders: [{ test: /\.vue$/, loader: "vue" }]
    }
}];
