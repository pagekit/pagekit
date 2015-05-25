var $ = require('jquery');

module.exports = {

    methods: {

        queryUpdates: function (api, packages) {

            var pkgs = {};

            $.each(packages, function (name, pkg) {
                pkgs[pkg.name] = pkg.version;
            });

            return this.$http.jsonp(api.url + '/package/update', {api_key: api.key, packages: JSON.stringify(pkgs)});
        },

        enablePackage: function (pkg) {
            return this.$http.post('admin/system/package/enable', {name: pkg.name}, function (data) {
                if (!data.error) {
                    pkg.$set('enabled', true);
                }
            });
        },

        disablePackage: function (pkg) {
            return this.$http.post('admin/system/package/disable', {name: pkg.name}, function (data) {
                if (!data.error) {
                    pkg.$set('enabled', false);
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
