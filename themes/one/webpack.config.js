module.exports = [

    {
        entry: {
            "widget-theme": "./app/components/widget-theme.vue",
            "settings": "./app/components/settings.vue"
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
