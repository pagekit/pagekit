module.exports = [

    {
        entry: {
            "node-meta": "./app/components/node-meta.vue"
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
