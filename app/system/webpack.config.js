var assets = __dirname + "/../../vendor/assets";

module.exports = [

    {
        entry: {
            "modules/editor/app/bundle/editor": "./modules/editor/app/editor",
            "modules/finder/app/bundle/finder": "./modules/finder/app/finder",
            "modules/package/app/bundle/extensions": "./modules/package/app/extensions",
            "modules/package/app/bundle/themes": "./modules/package/app/themes",
            "modules/package/app/bundle/marketplace": "./modules/package/app/marketplace",
            "modules/package/app/bundle/upload": "./modules/package/app/components/upload.vue",
            "modules/cache/app/bundle/settings": "./modules/cache/app/components/settings.vue",
            "modules/mail/app/bundle/settings": "./modules/mail/app/components/settings.vue",
            "modules/oauth/app/bundle/settings": "./modules/oauth/app/components/settings.vue",
            "app/bundle/imagepicker": "./app/components/imagepicker.vue"
        },
        output: {
            filename: "./[name].js"
        },
        externals: {
            "vue": "Vue",
            "lodash": "_",
            "jquery": "jQuery",
            "uikit": "UIkit",
            "settings": "Settings"
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
            "system.settings": "./modules/settings/app/settings"
        },
        output: {
            filename: "./modules/settings/app/bundle/settings.js",
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
    },

    {
        entry: {
            "vue": "./app/vue"
        },
        output: {
            filename: "./app/bundle/[name].js",
        },
        externals: {
            "vue": "Vue",
            "lodash": "_",
            "jquery": "jQuery",
        },
        resolve: {
            alias: {
                "md5$": assets + "/js-md5/js/md5.min.js",
                "vue-resource$": assets + "/vue-resource/src/index.js",
                "vue-validator$": assets + "/vue-validator/src/index.js",
            }
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
