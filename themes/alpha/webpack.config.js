module.exports = [

    {
        entry: {
            "widgets": "./app/components/widgets/theme.vue"
        },
        output: {
            filename: "./app/bundle/[name].js"
        },
        externals: {
            "site": "Edit"
        },
        module: {
            loaders: [
                { test: /\.vue$/, loader: "vue" }
            ]
        }
    }

];
