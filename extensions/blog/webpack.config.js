module.exports = [

    {
        entry: {
            "comment-index": "./app/views/admin/comment-index",
            "post-edit": "./app/views/admin/post-edit",
            "post-index": "./app/views/admin/post-index",
            "settings": "./app/views/admin/settings",
            "site": "./app/components/site"
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
