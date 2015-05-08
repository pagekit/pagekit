module.exports = [

    {
        entry: {
            "widgets/login": "./app/components/widgets/login.vue"
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
