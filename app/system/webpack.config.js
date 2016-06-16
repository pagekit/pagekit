var assets = __dirname + "/../assets";

module.exports = [

    {
        entry: {
            "vue": "./app/vue"
        },
        output: {
            filename: "./app/bundle/[name].js"
        },
        resolve: {
            alias: {
                "md5$": assets + "/js-md5/js/md5.js",
                "vue-form$": assets + "/vue-form/dist/vue-form.common.js",
                "vue-intl$": assets + "/vue-intl/dist/vue-intl.common.js",
                "vue-resource$": assets + "/vue-resource/dist/vue-resource.common.js",
                "JSONStorage$": assets + "/JSONStorage/storage.js"
            }
        },
        module: {
            loaders: [
                { test: /\.vue$/, loader: "vue" },
                { test: /\.json$/, loader: "json" },
                { test: /\.html$/, loader: "vue-html" }
            ]
        }
    }

];
