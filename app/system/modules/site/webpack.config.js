module.exports = [

    {
        entry: {
            "index": "./app/index",
            "edit": "./app/edit"
        },
        output: {
            filename: "./app/bundle/[name].js",
            library: "Site"
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
