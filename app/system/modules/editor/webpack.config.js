module.exports = [

    {
        entry: {
            "editor": "./app/editor"
        },
        output: {
            filename: "./app/bundle/[name].js",
            library: "Editor"
        },
        externals: {
            "lodash": "_",
            "jquery": "jQuery",
            "uikit": "UIkit",
            "vue": "Vue"
        },
        module: {
            loaders: [
                { test: /\.html$/, loader: "html" },
                { test: /\.vue$/, loader: "vue" }
            ]
        }
    }

];
