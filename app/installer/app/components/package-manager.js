module.exports = {

    mixins: [
        require('../lib/package')
    ],

    data: function () {
        return _.extend({
            package: {},
            view: false,
            updates: null,
            search: '',
            status: ''
        }, window.$data);
    },

    ready: function () {
        this.load();
    },

    methods: {

        load: function () {
            this.$set('status', 'loading');

            this.queryUpdates(this.packages, function (res) {
                var data = res.data;
                this.$set('updates', data.packages.length ? _.indexBy(data.packages, 'name') : null);
                this.$set('status', '');
            }).error(function () {
                this.$set('status', 'error');
            });
        },

        icon: function (pkg) {

            if (pkg.extra && pkg.extra.icon) {
                return pkg.url + '/' + pkg.extra.icon;
            } else {
                return this.$url('app/system/assets/images/placeholder-icon.svg');
            }

        },

        image: function (pkg) {

            if (pkg.extra && pkg.extra.image) {
                return pkg.url + '/' + pkg.extra.image;
            } else {
                return this.$url('app/system/assets/images/placeholder-800x600.svg');
            }

        },

        details: function (pkg) {
            this.$set('package', pkg);
            this.$refs.details.open();
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
                this.$refs.settings.open();

            } else {
                window.location = pkg.settings;
            }

        },

        update: function (pkg) {
            var vm = this;

            this.install(pkg, this.packages, function (output) {
                if (output.status === 'success') {
                    vm.updates.$delete(pkg.name);
                }

                setTimeout(function () {
                    location.reload();
                }, 300);
            });
        }

    },

    filters: {

        empty: function (packages) {
            return Vue.filter('filterBy')(packages, this.search, 'title').length === 0;
        }

    },

    components: {

        'package-upload': require('./package-upload.vue')

    }

};
