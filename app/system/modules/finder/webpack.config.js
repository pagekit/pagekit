module.exports = [

    {
        entry: {
            "finder": "./app/components/finder.vue"
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
    }

];
