module.exports = [

    {
        entry: {
            "extensions": "./app/views/extensions",
            "marketplace": "./app/views/marketplace",
            "themes": "./app/views/themes",
            "update": "./app/views/update"
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
