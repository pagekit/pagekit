module.exports = [

    {
        entry: {
            "interceptor": "./app/interceptor",
            "registration": "./app/views/registration",
            "profile": "./app/views/profile",
            "permission-index": "./app/views/admin/permission-index",
            "role-index": "./app/views/admin/role-index",
            "settings": "./app/views/admin/settings",
            "user-edit": "./app/views/admin/user-edit",
            "user-index": "./app/views/admin/user-index",
            "widget-login": "./app/components/widget-login.vue",
            "widget-user": "./app/components/widget-user.vue",
            "link-user": "./app/components/link-user.vue"
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
