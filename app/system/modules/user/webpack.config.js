module.exports = [

    {
        entry: {
            "edit": "./app/views/admin/edit",
            "index": "./app/views/admin/index",
            "permissions": "./app/views/admin/permissions",
            "roles": "./app/views/admin/roles",
            "settings": "./app/views/admin/settings",
            "widget-login": "./app/components/widget-login.vue",
            "widget-user": "./app/components/widget-user.vue"
        },
        output: {
            filename: "./app/bundle/[name].js"
        },
        module: {
            loaders: [
                { test: /\.vue$/, loader: "vue" }
            ]
        }
    }

];
