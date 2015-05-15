module.exports = [

    {
        entry: {
            "admin/index": "./app/admin/index"
        },
        output: {
            filename: "./app/bundle/[name].js",
            library: "Dashboard"
        },
        externals: {
            "lodash": "_",
            "jquery": "jQuery",
            "uikit": "UIkit",
            "vue": "Vue"
        },
        module: {
            loaders: [
                { test: /\.vue$/, loader: "vue" }
            ]
        }
    }

];
