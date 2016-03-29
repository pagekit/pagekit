module.exports = [

    {
        entry: {
            "panel-finder": "./app/components/panel-finder.vue",
            "link-storage": "./app/components/link-storage.vue"
        },
        output: {
            filename: "./app/bundle/[name].js",
            library: "Finder"
        },
        module: {
            loaders: [
                { test: /\.html$/, loader: "vue-html" },
                { test: /\.vue$/, loader: "vue" }
            ]
        }
    }

];
