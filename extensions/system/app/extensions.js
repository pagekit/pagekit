jQuery(function ($) {

    var vm = new Vue({

        el: '#extensions',

        data: $.extend($extensions, {
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
                    img = this.$url('extensions/:name/:image', {name: pkg.name, image: pkg.extra.image}, true);
                } else {
                    img = this.$url('extensions/system/assets/images/placeholder-icon.svg', true);
                }

                return img;
            },

            load: function () {

                var url = this.api.url + '/package/update', packages = {};

                $.each(this.packages, function (name, pkg) {
                    packages[pkg.name] = pkg.version;
                });

                this.$set('status', 'loading');

                $.post(url, {'api_key': this.api.key, 'packages': JSON.stringify(packages)}, function (data) {
                    vm.$set('updates', data.packages.length ? data.packages : null);
                    vm.$set('status', '');
                }, 'jsonp').fail(function () {
                    vm.$set('status', 'error');
                });
            },

            enable: function (pkg) {
                this.$http.post('admin/system/extensions/enable', {name: pkg.name}, function (data) {

                    if (!data.error) {
                        pkg.enabled = true;
                    }

                    UIkit.notify(data.message, data.error ? 'danger' : 'success');

                });
            },

            disable: function (pkg) {
                this.$http.post('admin/system/extensions/disable', {name: pkg.name}, function (data) {

                    if (!data.error) {
                        pkg.enabled = false;
                    }

                    UIkit.notify(data.message, data.error ? 'danger' : 'success');

                });
            },

            uninstall: function (pkg) {
                this.$http.post('admin/system/extensions/uninstall', {name: pkg.name}, function (data) {

                    if (!data.error) {
                        vm.packages.splice(vm.packages.indexOf(pkg), 1);
                    }

                    UIkit.notify(data.message, data.error ? 'danger' : 'success');

                });
            }

        }

    });

});
