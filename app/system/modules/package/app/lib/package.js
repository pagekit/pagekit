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

            var name = _.isObject(pkg) ? pkg.name : pkg;

            return this.$http.jsonp(this.api.url + '/package/:name', {api_key: this.api.key, name: name}, success);
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
            return this.$http.post('admin/system/package/install',  {package: pkg.version}, function (data) {
                if (packages && data.message) {
                    packages.push(pkg);
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
