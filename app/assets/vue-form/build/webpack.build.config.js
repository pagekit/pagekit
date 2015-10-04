var webpack = require("webpack");
var version = require("../package.json").version;
var banner =
    "/**\n" +
    " * vue-form v" + version + "\n" +
    " * Released under the MIT License.\n" +
    " */\n";

module.exports = [

    {
        entry: "./src/index",
        output: {
            path: "./dist",
            filename: "vue-form.js",
            library: "VueForm",
            libraryTarget: "umd"
        },
        module: {
            loaders: [
                { test: /\.html$/, loader: "html" }
            ]
        },
        plugins: [
            new webpack.BannerPlugin(banner, {raw: true})
        ]
    },

    {
        entry: "./src/index",
        output: {
            path: "./dist",
            filename: "vue-form.min.js",
            library: "VueForm",
            libraryTarget: "umd"
        },
        module: {
            loaders: [
                { test: /\.html$/, loader: "html" }
            ]
        },
        plugins: [
            new webpack.optimize.UglifyJsPlugin,
            new webpack.BannerPlugin(banner, {raw: true})
        ]
    }

];
