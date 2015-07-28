module.exports = [

    {
        entry: {
            "site-theme": "./app/components/site-theme.vue",
            "site-appearance": "./app/components/site-appearance.vue",
            "widget-appearance": "./app/components/widget-appearance.vue"
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
