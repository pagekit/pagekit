var Output = require('./output.vue');

module.exports = {

    data: function () {
        return _.extend(window.$data, {
            package: {},
            view: false,
            updates: null,
            search: '',
            status: ''
        });
    },

    ready: function () {
        this.load();
    },

    components: {

        'package-details': require('./package-details.vue'),
        'package-upload': require('./package-upload.vue')

    },

    methods: {

        load: function () {
            this.$set('status', 'loading');

            this.queryUpdates(this.packages, function (data) {
                this.$set('updates', data.packages.length ? _.indexBy(data.packages, 'name') : null);
                this.$set('status', '');
            }).error(function () {
                this.$set('status', 'error');
            });
        },

        icon: function (pkg) {

            if (pkg.extra && pkg.extra.image) {
                return pkg.url + '/' + pkg.extra.image;
            } else {
                return this.$url('app/system/assets/images/placeholder-icon.svg');
            }

        },

        details: function (pkg) {
            this.$set('package', pkg);
            this.$.details.open();
        },

        settings: function (pkg) {

            if (!pkg.settings) {
                return;
            }

            var view, options;

            _.forIn(this.$options.components, function (component, name) {

                options = component.options || {};

                if (options.settings && pkg.settings === name) {
                    view = name;
                }

            });

            if (view) {

                this.$set('package', pkg);
                this.$set('view', view);
                this.$.settings.open();

            } else {
                window.location = pkg.settings;
            }

        },

        enable: function (pkg) {
            this.enablePackage(pkg).success(function () {
                this.$notify(this.$trans('"%title%" enabled.', {title: pkg.title}));
            }).error(this.error);
        },

        disable: function (pkg) {
            this.disablePackage(pkg).success(function () {
                this.$notify(this.$trans('"%title%" disabled.', {title: pkg.title}));
            }).error(this.error);
        },

        uninstall: function (pkg) {
            this.uninstallPackage(pkg, this.packages);
        },

        update: function (pkg) {
            var vm = this;

            this.installPackage(pkg, this.packages, function (output) {
                if (output.status === 'success') {
                    vm.updates.$delete(pkg.name);
                }

                setTimeout(function () {
                    location.reload();
                }, 300);
            });
        },

        error: function (message) {
            this.$notify(message, 'danger');
        }

    },

    filters: {

        empty: function (packages) {
            return Vue.filter('filterBy')(packages, this.search, 'title').length === 0;
        }

    },

    mixins: [
        require('../lib/package')
    ]

};