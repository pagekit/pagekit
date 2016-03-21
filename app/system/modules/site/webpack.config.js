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
                { test: /\.vue$/, loader: "vue" }
            ]
        }
    },

    {
        entry: {
            "edit": "./app/views/edit",
            "index": "./app/views/index",
            "input-link": "./app/components/input-link.vue",
            "input-tree": "./app/components/input-tree.vue",
            "link-page": "./app/components/link-page.vue",
            "node-page": "./app/components/node-page.vue",
            "node-meta": "./app/components/node-meta.vue",
            "settings": "./app/views/settings",
            "widget-menu": "./app/components/widget-menu.vue",
            "widget-text": "./app/components/widget-text.vue"
        },
        output: {
            filename: "./app/bundle/[name].js"
        },
        module: {
            loaders: [
                { test: /\.html$/, loader: "vue-html" },
                { test: /\.vue$/, loader: "vue" }
            ]
        }
    }

];
