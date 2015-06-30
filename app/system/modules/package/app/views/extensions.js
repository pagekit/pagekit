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

            if (pkg.extra && pkg.extra.image) {
                return this.$url.static('extensions/:name/:image', {name: pkg.name, image: pkg.extra.image});
            } else {
                return this.$url.static('app/system/assets/images/placeholder-icon.svg');
            }

        },

        load: function () {

            var vm = this;

            this.$set('status', 'loading');

            this.queryUpdates(this.packages, function (data) {
                vm.$set('updates', data.packages.length ? data.packages : null);
                vm.$set('status', '');
            }).error(function () {
                vm.$set('status', 'error');
            });
        },

        details: function (pkg) {
            this.$set('package', pkg);
            this.modal.show();
        },

        enable: function (pkg) {
            this.enablePackage(pkg).success(function (data) {
                UIkit.notify(this.$trans('"%title%" enabled.', {title: pkg.title}));
            }).error(this.error);
        },

        disable: function (pkg) {
            this.disablePackage(pkg).success(function (data) {
                UIkit.notify(this.$trans('"%title%" disabled.', {title: pkg.title}));
            }).error(this.error);
        },

        uninstall: function (pkg) {
            this.uninstallPackage(pkg, this.packages).success(function (data) {
                UIkit.notify(this.$trans('"%title%" uninstalled.', {title: pkg.title}));
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
        'package-upload':  require('../components/package-upload.vue')

    }

};

$(function () {

    new Vue(module.exports).$mount('#extensions');

});
