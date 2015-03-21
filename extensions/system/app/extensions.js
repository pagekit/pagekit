jQuery(function ($) {

    var vm = new Vue({

        el: '#extensions',

        data: $.extend(extensions, {
            loading: true,
            error: false,
            updates: null,
            search: ''
        }),

        ready: function () {

            this.load();

        },

        methods: {

            icon: function (pkg) {

                var img;

                if (pkg.extra.image) {
                    img = this.$url('extensions/:name/:image', {name: pkg.name, image: pkg.extra.image}, true);
                } else {
                    img = this.$url('extensions/system/assets/images/placeholder-icon.svg', true);
                }

                return img;
            },

            load: function () {

                var url = this.api.url + '/package/update', packages = {};

                $.each(this.packages, function(name, pkg) {
                    packages[pkg.name] = pkg.version;
                });

                $.post(url, {'api_key': this.api.key, 'packages': JSON.stringify(packages)}, function(data) {
                    vm.$set('updates', data.packages.length ? data.packages : null);
                }, 'jsonp').fail(function() {
                    vm.$set('error', true);
                }).always(function() {
                    vm.$set('loading', false);
                });
            },

            enable: function (pkg) {

            },

            disable: function (pkg) {

            },

            uninstall: function (pkg) {

            }

        }

    });

});
