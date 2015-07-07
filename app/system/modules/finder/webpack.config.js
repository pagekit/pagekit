module.exports = [

    {
        entry: {
            "panel-finder": "./app/components/panel-finder.vue"
        },
        output: {
            filename: "./app/bundle/[name].js",
            library: "Finder"
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
            "input-image": "./app/components/input-image.vue"
        },
        output: {
            filename: "./app/bundle/[name].js"
        },
        module: {
            loaders: [
                { test: /\.vue$/, loader: "vue" }
            ]
        }
    }

];
