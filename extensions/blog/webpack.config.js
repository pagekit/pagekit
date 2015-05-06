module.exports = [

    {
        entry: {
            "site.blog": "./app/components/site.blog.vue",
            "site.post": "./app/components/site.post.vue"
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
