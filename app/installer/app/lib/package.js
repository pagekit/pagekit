var Output = require('../components/output.vue');

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
            }, success).error(function () {});
        },

        enablePackage: function (pkg, reload) {
            return this.$http.post('admin/system/package/enable', {name: pkg.name}, function (data) {
                if (!data.error) {
                    pkg.$set('enabled', true);
                    if (reload !== false) {
                        document.location.reload();
                    }
                }
            });
        },

        disablePackage: function (pkg, reload) {
            return this.$http.post('admin/system/package/disable', {name: pkg.name}, function (data) {

                if (!data.error) {
                    pkg.$set('enabled', false);
                    if (reload !== false) {
                        document.location.reload();
                    }
                }
            });
        },

        installPackage: function (pkg, packages, onClose) {
            var output = this.$addChild(Output);
            if (onClose) {
                output.onClose(onClose);
            }

            return this.$http.post('admin/system/package/install', {package: pkg}, null, {
                beforeSend: function (request) {
                    output.init(request, this.$trans('Installing %title% %version%', {title: pkg.title, version: pkg.version}));
                }
            }).success(function () {
                if (output.status === 'success' && packages) {

                    var index = _.findIndex(packages, 'name', pkg.name);

                    if (-1 !== index) {
                        packages.splice(index, 1, pkg);
                    } else {
                        packages.push(pkg);
                    }

                }
            }).error(function (msg) {
                output.close();
                this.$notify(msg, 'danger');
            });
        },

        uninstallPackage: function (pkg, packages) {
            var output = this.$addChild(Output);

            return this.$http.post('admin/system/package/uninstall', {name: pkg.name}, null, {
                beforeSend: function (request) {
                    output.init(request, this.$trans('Uninstalling %title% %version%', {title: pkg.title, version: pkg.version}));
                }
            }).success(function () {
                if (output.status === 'success' && packages) {
                    packages.splice(packages.indexOf(pkg), 1);
                }
            }).error(function (message) {
                output.close();
                this.$notify(message, 'danger');
            });
        }

    }

};
