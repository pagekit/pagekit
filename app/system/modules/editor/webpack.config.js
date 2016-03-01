module.exports = [

    {
        entry: {
            "editor": "./app/components/editor.vue"
        },
        output: {
            filename: "./app/bundle/[name].js",
            library: "Editor"
        },
        module: {
            loaders: [
                { test: /\.vue$/, loader: "vue" }
            ]
        }
    }

];
