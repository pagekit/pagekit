module.exports = [

    {
        entry: {
            "settings": "./app/settings"
        },
        output: {
            filename: "./app/bundle/[name].js",
            library: "Settings"
        },
        externals: {
            "vue": "Vue",
            "lodash": "_",
            "jquery": "jQuery",
            "uikit": "UIkit"
        },
        module: {
            loaders: [
                { test: /\.html$/, loader: "html" },
                { test: /\.vue$/, loader: "vue" }
            ]
        }
    }

];
