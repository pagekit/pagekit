var assets = __dirname + "/../../vendor/assets";

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
                "md5$": assets + "/js-md5/js/md5.min.js",
                "vue-resource$": assets + "/vue-resource/src/index.js",
                "vue-validator$": assets + "/vue-validator/src/index.js",
                "promise$": assets + "/vue-resource/src/lib/promise.js"
            }
        },
        module: {
            loaders: [
                { test: /\.html$/, loader: "html" },
                { test: /\.vue$/, loader: "vue" }
            ]
        }
    },

    {
        entry: {
            "globalize": "./app/globalize"
        },
        output: {
            filename: "./app/bundle/[name].js",
            library: "Globalize"
        },
        resolve: {
            alias: {
                "cldr$": assets + "/cldrjs/dist/cldr.js",
                "cldr/event$": assets + "/cldrjs/dist/cldr/event.js",
                "cldr/supplemental$": assets + "/cldrjs/dist/cldr/supplemental.js",
                "globalize$": assets + "/globalize/dist/globalize.js",
                "globalize/number$": assets + "/globalize/dist/globalize/number.js",
                "globalize/date$": assets + "/globalize/dist/globalize/date.js",
                "globalize/relative-time$": assets + "/globalize/dist/globalize/relative-time.js"
            }
        }
    }

];
