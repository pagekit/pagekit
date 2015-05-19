module.exports = [

    {
        entry: {
            "widgets/login": "./app/widgets/login.vue",
            "widgets/user": "./app/widgets/user.vue",
            "admin/roles": "./app/admin/roles",
            "admin/permissions": "./app/admin/permissions"
        },
        output: {
            filename: "./app/bundle/[name].js"
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
