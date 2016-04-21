module.exports = {

    mixins: [
        require('../lib/package')
    ],

    data: function () {

        return _.extend({
            package: {},
            view: false,
            updates: null,
            search: this.$session.get(this.$options.name + '.search', ''),
            status: ''
        }, window.$data);
    },

    ready: function () {
        this.load();
    },

    watch: {
        search: function (search) {
            this.$session.set(this.$options.name + '.search', search);
        }
    },

    methods: {

        load: function () {
            this.$set('status', 'loading');

            if (this.packages) {
                this.queryUpdates(this.packages).then(function (res) {
                    var data = res.data;
                    this.$set('updates', data.packages.length ? _.indexBy(data.packages, 'name') : null);
                    this.$set('status', '');
                }, function () {
                    this.$set('status', 'error');
                });
            }
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

        }

    },

    filters: {

        empty: function (packages) {
            return Vue.filter('filterBy')(packages, this.search, 'title').length === 0;
        }

    },

    components: {

        'package-upload': require('./package-upload.vue'),
        'package-details': require('./package-details.vue')

    }

};
