module.exports = [

    {
        entry: {
            "comment-index": "./app/views/admin/comment-index",
            "post-edit": "./app/views/admin/post-edit",
            "post-index": "./app/views/admin/post-index",
            "settings": "./app/views/admin/settings",
            "comments": "./app/views/comments",
            "post": "./app/views/post",
            "posts": "./app/views/posts",
            "node-blog": "./app/components/node-blog.vue",
            "link-blog": "./app/components/link-blog.vue"
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
