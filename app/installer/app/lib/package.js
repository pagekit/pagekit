var Install = Vue.extend(require('./install.vue'));
var Uninstall = Vue.extend(require('./uninstall.vue'));
var Update = Vue.extend(require('./update.vue'));

module.exports = {

    methods: {

        queryUpdates: function (packages, success) {

            var pkgs = {}, options = {emulateJSON: true};

            _.each(packages, function (pkg) {
                pkgs[pkg.name] = pkg.version;
            });

            return this.$http.post(this.api + '/api/package/update', {
                packages: JSON.stringify(pkgs)
            }, options).then(success, this.error);
        },

        enable: function (pkg) {
            return this.$http.post('admin/system/package/enable', {name: pkg.name}).then(function () {
                    this.$notify(this.$trans('"%title%" enabled.', {title: pkg.title}));
                    Vue.set(pkg, 'enabled', true);
                    document.location.assign(this.$url('admin/system/package/' + (pkg.type === 'pagekit-theme' ? 'themes' : 'extensions')));
                }, this.error);
        },

        disable: function (pkg) {
            return this.$http.post('admin/system/package/disable', {name: pkg.name})
                .then(function () {
                    this.$notify(this.$trans('"%title%" disabled.', {title: pkg.title}));
                    Vue.set(pkg, 'enabled', false);
                    document.location.reload();
                }, this.error);
        },

        install: function (pkg, packages, onClose, packagist) {
            var install = new Install({parent: this});

            return install.install(pkg, packages, onClose, packagist);
        },

        update: function (pkg, updates, onClose, packagist) {
            var update = new Update({parent: this});

            return update.update(pkg, updates, onClose, packagist);
        },

        uninstall: function (pkg, packages) {
            var uninstall = new Uninstall({parent: this});

            return uninstall.uninstall(pkg, packages);
        },

        error: function (message) {
            this.$notify(message.data, 'danger');
        }

    }

};
