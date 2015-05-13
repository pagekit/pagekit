module.exports = [

    {
        entry: {
            "admin/edit": "./app/admin/edit"
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
