module.exports = [

    {
        entry: {
            "site": "./app/components/site"
        },
        output: {
            filename: "./app/bundle/[name].js",
            library: "Widgets"
        },
        externals: {
            "vue": "Vue",
            "site": "Site"
        },
        module: {
            loaders: [
                { test: /\.html$/, loader: "html" },
                { test: /\.vue$/, loader: "vue" }
            ]
        }
    }

];
