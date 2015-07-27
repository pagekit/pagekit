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
                return this.$url.static('app/system/assets/images/placeholder-800x600.svg');
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

            var view;
            _.forIn(this.$options.components, function (component, name) {
                if (component.options.settings && pkg.settings === name) {
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
                UIkit.notify(this.$trans('"%title%" enabled.', {title: pkg.title}));
            }).error(this.error);
        },

        uninstall: function (pkg) {
            this.uninstallPackage(pkg, this.packages).success(function () {
                UIkit.notify(this.$trans('"%title%" uninstalled.', {title: pkg.title}));
            }).error(this.error);
        },

        update: function (pkg) {
            this.installPackage(pkg, this.packages).success(function () {
                UIkit.notify(this.$trans('"%title%" updated.', {title: pkg.title}));
                this.load();
            }).error(this.error);
        },

        error: function (message) {
            UIkit.notify(message, 'danger');
        }

    },

    filters: {

        empty: function (packages) {
            return Vue.filter('filterBy')(packages, this.search, 'title').length === 0;
        },

        themeorder: function(packages) {

            var index = packages.indexOf(_.find(packages, {enabled:true}));

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
