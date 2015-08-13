module.exports = [

    {
        entry: {
            "panel-link": "./app/components/panel-link.vue"
        },
        output: {
            filename: "./app/bundle/[name].js",
            library: "Links"
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
            "edit": "./app/views/edit",
            "index": "./app/views/index",
            "input-link": "./app/components/input-link.vue",
            "link-page": "./app/components/link-page.vue",
            "node-page": "./app/components/node-page.vue",
            "settings": "./app/views/settings",
            "widget-menu": "./app/components/widget-menu.vue",
            "widget-text": "./app/components/widget-text.vue"
        },
        output: {
            filename: "./app/bundle/[name].js"
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
