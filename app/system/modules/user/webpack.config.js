module.exports = [

    {
        entry: {
            "widgets/login": "./app/widgets/login.vue",
            "admin/widgets/user": "./app/admin/widgets/user.vue"
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
