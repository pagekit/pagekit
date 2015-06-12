module.exports = [

    {
        entry: {
            "edit": "./app/views/edit",
            "index": "./app/views/index",
            "settings": "./app/views/settings"
        },
        output: {
            filename: "./app/bundle/[name].js",
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
