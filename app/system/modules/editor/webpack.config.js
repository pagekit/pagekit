module.exports = [

    {
        entry: {
            "editor": "./app/components/editor.vue"
        },
        output: {
            filename: "./app/bundle/[name].js",
            library: "Editor"
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
