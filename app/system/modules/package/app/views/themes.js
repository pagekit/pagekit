window.Themes = module.exports = {

    data: function () {
        return _.extend(window.$data, {
            package: {},
            view: '',
            updates: null,
            search: '',
            status: ''
        })
    },

    ready: function () {
        this.load();
    },

    methods: {

        icon: function (pkg) {

            if (pkg.extra && pkg.extra.image) {
                return pkg.url + '/' + pkg.extra.image;
            } else {
                return this.$url('app/system/assets/images/placeholder-800x600.svg');
            }

        },

        load: function () {
            this.$set('status', 'loading');

            this.queryUpdates(this.packages, function (data) {
                this.$set('updates', data.packages.length ? _.indexBy(data.packages, 'name') : null);
                this.$set('status', '');
            }).error(function () {
                this.$set('status', 'error');
            });
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

        uninstall: function (pkg) {
            this.uninstallPackage(pkg, this.packages);
        },

        update: function (pkg) {
            this.disablePackage(pkg, false).success(function () {

                var vm = this;
                this.installPackage(pkg, this.packages,
                    function (output) {
                        if (output.status !== 'success') {
                            return;
                        }

                        vm.enablePackage(pkg).success(function () {
                            vm.$notify(vm.$trans('"%title%" enabled.', {title: pkg.title}));
                        }).error(vm.error);
                    });

            }).error(this.error);
        },

        error: function (message) {
            this.$notify(message, 'danger');
        }

    },

    filters: {

        empty: function (packages) {
            return Vue.filter('filterBy')(packages, this.search, 'title').length === 0;
        },

        themeorder: function (packages) {

            var index = packages.indexOf(_.find(packages, {enabled: true}));

            if (index !== -1) {
                packages.splice(0, 0, packages.splice(index, 1)[0]);
            }

            return packages;
        }

    },

    components: {

        'package-details': require('../components/package-details.vue'),
        'package-upload': require('../components/package-upload.vue')

    },

    mixins: [
        require('../lib/package')
    ]

};

jQuery(function () {

    (new Vue(module.exports)).$mount('#themes');

});
