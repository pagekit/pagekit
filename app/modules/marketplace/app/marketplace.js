(function ($, Vue) {

    var _ = Vue.util;

    Vue.component('v-marketplace', {

        replace: true,

        template: '#marketplace.main',

        data: function () {
            return {
                api: {},
                search: '',
                type: 'extension',
                pkg: null,
                packages: null,
                installed: [],
                page: 0,
                pages: 0,
                iframe: '',
                status: ''
            };
        },

        ready: function () {
            this.query();
        },

        watch: {

            search: function () {
                this.query();
            },

            page: function () {
                this.query(this.page);
            }

        },

        methods: {

            query: function (page) {

                var vm = this, url = this.api.url + '/package/search';

                $.post(url, {q: this.search, type: this.type, page: page || 0}, function (data) {
                    vm.$set('packages', data.packages);
                    vm.$set('pages', data.pages);
                }, 'jsonp').fail(function () {
                    vm.$set('packages', null);
                    vm.$set('status', 'error');
                });
            },

            details: function (pkg) {

                if (!this.modal) {
                    this.modal = UIkit.modal(this.$$.modal);
                }

                this.$set('iframe', this.api.url.replace(/\/api$/, '') + '/marketplace/frame/' + pkg.name);
                this.$set('pkg', pkg);

                this.modal.show();
            },

            install: function (pkg) {

                var vm = this, url = this.$url('admin/system/package/install');

                vm.$set('status', 'installing');

                $.post(url, {'package': JSON.stringify(pkg.version)}, function (data) {

                    if (data.message) {
                        vm.installed.push(pkg);
                    } else {
                        UIkit.notify(data.error, 'danger');
                    }

                    vm.$set('status', '');
                });
            },

            isInstalled: function (pkg) {
                return _.isObject(pkg) ? _.findBy(this.installed, 'name', pkg.name) : undefined;
            }
        }

    });

})(jQuery, Vue);
