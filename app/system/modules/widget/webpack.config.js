module.exports = [

    {
        entry: {
            "widgets": "./app/components/widgets"
        },
        output: {
            filename: "./app/bundle/[name].js",
            library: "Widgets"
        }
    },

    {
        entry: {
            "site": "./app/components/site"
        },
        output: {
            filename: "./app/bundle/[name].js"
        },
        externals: {
            "lodash": "_",
            "jquery": "jQuery",
            "uikit": "UIkit",
            "vue": "Vue",
            "site": "Site",
            "widgets": "Widgets"
        },
        module: {
            loaders: [
                { test: /\.html$/, loader: "html" },
                { test: /\.vue$/, loader: "vue" }
            ]
        }
    }

];
