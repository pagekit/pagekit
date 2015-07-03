module.exports = {

    mixins: [
        require('../lib/package')
    ],

    data: _.extend(window.$data, {
        package: {},
        updates: null,
        search: '',
        status: ''
    }),

    ready: function () {
        this.load();
        this.modal = UIkit.modal(this.$$.details);
    },

    methods: {

        icon: function (pkg) {
            if (pkg.extra.image) {
                return this.$url.static('themes/:name/:image', {name: pkg.name, image: pkg.extra.image});
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
            this.modal.show();
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
        }

    },

    components: {

        'package-details': require('../components/package-details.vue'),
        'package-upload': require('../components/package-upload.vue')

    }

};

$(function () {

    new Vue(module.exports).$mount('#themes');

});
