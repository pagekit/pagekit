var webpack = require("webpack");
var version = require("../package.json").version;
var banner =
    "/**\n" +
    " * vue-intl v" + version + "\n" +
    " * Released under the MIT License.\n" +
    " */\n";

module.exports = [

    {
        entry: "./src/index",
        output: {
            path: "./dist",
            filename: "vue-intl.js",
            library: "VueIntl",
            libraryTarget: "umd"
        },
        module: {
            loaders: [
                { test: /\.json$/, loader: "json" }
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
            filename: "vue-intl.min.js",
            library: "VueIntl",
            libraryTarget: "umd"
        },
        module: {
            loaders: [
                { test: /\.json$/, loader: "json" }
            ]
        },
        plugins: [
            new webpack.optimize.UglifyJsPlugin,
            new webpack.BannerPlugin(banner, {raw: true})
        ]
    }

];
