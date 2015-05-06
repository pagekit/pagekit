module.exports = [

    {
        entry: {
            "site": "./app/site"
        },
        output: {
            filename: "./app/bundle/site.js",
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
