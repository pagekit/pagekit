module.exports = [

    {
        entry: {
            "widgets/menu": "./app/components/widgets/menu.vue"
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
