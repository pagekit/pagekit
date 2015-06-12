module.exports = [

    {
        entry: {
            "index": "./app/views/index"
        },
        output: {
            filename: "./app/bundle/[name].js",
            library: "Dashboard"
        },
        externals: {
            "lodash": "_",
            "jquery": "jQuery",
            "uikit": "UIkit",
            "vue": "Vue"
        },
        module: {
            loaders: [
                { test: /\.vue$/, loader: "vue" }
            ]
        }
    }

];
