module.exports = {

    methods: {

        queryUpdates: function (packages, success) {

            var pkgs = {};

            _.each(packages, function (pkg) {
                pkgs[pkg.name] = pkg.version;
            });

            return this.$http.jsonp(this.api.url + '/package/update', {api_key: this.api.key, packages: JSON.stringify(pkgs)}, success);
        },

        queryPackage: function (pkg, success) {
            return this.$http.jsonp(this.api.url + '/package/:name', {api_key: this.api.key, name: _.isObject(pkg) ? pkg.name : pkg}, success);
        },

        enablePackage: function (pkg) {
            return this.$http.post('admin/system/package/enable', {name: pkg.name}, function (data) {
                if (!data.error) {
                    pkg.$set('enabled', true);
                    document.location.reload();
                }
            });
        },

        disablePackage: function (pkg) {
            return this.$http.post('admin/system/package/disable', {name: pkg.name}, function (data) {
                if (!data.error) {
                    pkg.$set('enabled', false);
                    document.location.reload();
                }
            });
        },

        installPackage: function (pkg, packages) {
            return this.$http.post('admin/system/package/install', {package: pkg}, function (data) {
                if (packages && data.package) {

                    var index = _.findIndex(packages, 'name', data.package.name);

                    if (-1 !== index) {
                        packages.splice(index, 1, data.package);
                    } else {
                        packages.push(data.package);
                    }

                }
            });
        },

        uninstallPackage: function (pkg, packages) {
            return this.$http.post('admin/system/package/uninstall', {name: pkg.name}, function (data) {
                if (packages && !data.error) {
                    packages.splice(packages.indexOf(pkg), 1);
                }
            });
        }

    }

};
