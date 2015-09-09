var Install = require('./install.vue');
var Uninstall = require('./uninstall.vue');

module.exports = {

    methods: {

        queryUpdates: function (packages, success) {

            var pkgs = {}, options = {emulateJSON: true};

            _.each(packages, function (pkg) {
                pkgs[pkg.name] = pkg.version;
            });

            return this.$http.post(this.api + '/api/package/update', {
                packages: JSON.stringify(pkgs)
            }, success, options);
        },

        queryPackage: function (pkg, success) {
            return this.$http.get(this.api + '/api/package/:name', {
                name: _.isObject(pkg) ? pkg.name : pkg
            }, success).error(function () {
            });
        },


        enable: function (pkg) {
            return this.$http.post('admin/system/package/enable', {name: pkg.name})
                .success(function () {
                    this.$notify(this.$trans('"%title%" enabled.', {title: pkg.title}));
                    pkg.$set('enabled', true);
                    document.location.reload();
                }).error(this.error);
        },

        disable: function (pkg) {
            return this.$http.post('admin/system/package/disable', {name: pkg.name})
                .success(function () {
                    pkg.$set('enabled', false);
                    document.location.reload();
                }).error(this.error);
        },

        install: function (pkg, packages) {
            var install = this.$addChild(Install);

            return install.install(pkg, packages);
        },

        uninstall: function (pkg, packages) {
            var uninstall = this.$addChild(Uninstall);

            return uninstall.uninstall(pkg, packages);
        },

        error: function (message) {
            this.$notify(message, 'danger');
        }

    }

};
