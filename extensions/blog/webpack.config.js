module.exports = [

    {
        entry: {
            "admin/comments": "./app/admin/comments",
            "admin/edit": "./app/admin/edit",
            "admin/index": "./app/admin/index",
            "admin/settings": "./app/admin/settings",
            "admin/site": "./app/admin/site"
        },
        output: {
            filename: "./app/bundle/[name].js"
        },
        externals: {
            "lodash": "_",
            "jquery": "jQuery",
            "uikit": "UIkit",
            "vue": "Vue",
            "site": "Site"
        },
        module: {
            loaders: [
                { test: /\.vue$/, loader: "vue" }
            ]
        }
    }

];
