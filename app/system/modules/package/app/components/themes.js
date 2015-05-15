var $ = require('jquery');

module.exports = {

    mixins: [
        require('./package')
    ],

    data: $.extend(window.$data, {
        updates: null,
        search: '',
        status: ''
    }),

    ready: function () {
        this.load();
    },

    methods: {

        icon: function (pkg) {

            var img;

            if (pkg.extra.image) {
                img = this.$url.static('themes/:name/:image', {name: pkg.name, image: pkg.extra.image});
            } else {
                img = this.$url.static('app/system/assets/images/placeholder-800x600.svg');
            }

            return img;
        },

        load: function () {

            var vm = this;

            this.$set('status', 'loading');

            this.queryUpdates(this.api, this.packages).done(function (data) {
                vm.$set('updates', data.packages.length ? data.packages : null);
                vm.$set('status', '');
            }).fail(function () {
                vm.$set('status', 'error');
            });
        },

        enable: function (pkg) {
            this.enablePackage(pkg).success(function (data) {
                UIkit.notify(data.message);
            }).error(function (data) {
                UIkit.notify(data, 'danger');
            });
        },

        uninstall: function (pkg) {
            this.uninstallPackage(pkg, this.packages).success(function (data) {
                UIkit.notify(this.$trans('"%title%" enabled.', {title: pkg.title}));
            }).error(function (data) {
                UIkit.notify(data, 'danger');
            });
        }

    }

};


