module.exports = [

    {
        entry: {
            "widgets/login": "./app/widgets/login.vue",
            "widgets/user": "./app/widgets/user.vue"
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
