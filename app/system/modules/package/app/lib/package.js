var Output = require('../components/output.vue');

module.exports = {

    methods: {

        queryUpdates: function (packages, success) {

            var pkgs = {};

            _.each(packages, function (pkg) {
                pkgs[pkg.name] = pkg.version;
            });

            return this.$http.jsonp(this.api.url + '/package/update', {
                api_key: this.api.key,
                packages: JSON.stringify(pkgs)
            }, success);
        },

        queryPackage: function (pkg, success) {
            return this.$http.jsonp(this.api.url + '/package/:name', {
                api_key: this.api.key,
                name: _.isObject(pkg) ? pkg.name : pkg
            }, success).error(function () {
            });
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
            var options = {
                xhr: {
                    onprogress: function () {
                        output.setOutput(this.responseText);
                    }
                }
            };

            output.init(this.$trans('Installing "%title%"', {title: this.package.title}));
            if (onClose) {
                output.onClose(onClose);
            }

            return this.$http.post('admin/system/package/install', {package: pkg}, function (data) {
                if (data.package) {
                    pkg = data.package;
                }

                if (packages) {

                    var index = _.findIndex(packages, 'name', pkg.name);

                    if (-1 !== index) {
                        packages.splice(index, 1, pkg);
                    } else {
                        packages.push(pkg);
                    }

                }
            }, options).error(function (msg) {

                console.log(msg);
                output.close();

                this.$notify(msg, 'danger');
            });
        },

        uninstallPackage: function (pkg, packages) {
            var output = this.$addChild(Output);
            var options = {
                xhr: {
                    onprogress: function () {
                        output.setOutput(this.responseText);
                    }
                }
            };

            output.init(this.$trans('Uninstalling "%title%"', {title: pkg.title}));

            return this.$http.post('admin/system/package/uninstall', {name: pkg.name}, function (data) {
                if (packages && !data.error) {
                    packages.splice(packages.indexOf(pkg), 1);
                }
            }, options).error(function (message) {
                output.close();
                this.$notify(message, 'danger');
            });
        }

    }

};
