module.exports = [

    {
        entry: {
            "widgets": "./app/admin/widget"
        },
        output: {
            filename: "./app/bundle/[name].js",
            library: "Widgets"
        },
        externals: {
            "vue": "Vue"
        },
        module: {
            loaders: [
                { test: /\.html$/, loader: "html" },
                { test: /\.vue$/, loader: "vue" }
            ]
        }
    }

];
