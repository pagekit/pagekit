jQuery(function ($) {

    var vm = new Vue({

        el: '#js-user',

        data: {
            config  : $config,
            data    : $data,
            users   : null,
            pages   : 0,
            selected: []
        },

        created: function () {

            this.resource = this.$resource('api/user/:id');
            this.config.filter = $.extend({ status: '', role: '', permission: '' }, this.config.filter ? this.config.filter : {});

            this.$watch('config.page', this.load, true, true);
            this.$watch('config.filter', function() { this.load(0); }, true);
        },

        computed: {

            statuses: function() {
                return [{ text: this.$trans('- Status -'), value: '' }, { text: this.$trans('New'), value: 'new' }].concat(
                    Vue.filter('toArray')($.map(this.data.statuses, function(status, id) { return { text: status, value: id }; }))
                );
            },

            roles: function() {
                return [{ text: this.$trans('- Role -'), value: '' }].concat(
                    this.data.roles.map(function(role) { return { text: role.name, value: role.id }; })
                );
            },

            permissions: function() {
                return [{ text: this.$trans('- Permission -'), value: '' }].concat(
                    Vue.filter('toArray')($.map(this.data.permissions, function (permissions, group) {
                        return {
                            label: group,
                            options: Vue.filter('toArray')($.map(permissions, function (permission, id) {
                                return { text: permission.title, value: id };
                            }))
                        };
                    }))
                );
            }

        },

        methods: {

            save: function (user) {
                this.resource.save({ id: user.id }, { user: user }, function (data) {
                    vm.load();
                    UIkit.notify(data.message || data.error, data.error ? 'danger' : 'success');
                });
            },

            status: function(status) {

                var users = this.getSelected();

                users.forEach(function(user) {
                    user.status = status;
                });

                this.resource.save({ id: 'bulk' }, { users: users }, function (data) {
                    vm.load();
                    UIkit.notify(data.message || data.error, data.error ? 'danger' : 'success');
                });
            },

            remove: function() {
                this.resource.delete({ id: 'bulk' }, { ids: this.selected }, function (data) {
                    vm.load();
                    UIkit.notify(data.message || data.error, data.error ? 'danger' : 'success');
                });
            },

            toggleStatus: function (user) {
                user.status = !!user.status ? 0 : 1;
                this.save(user);
            },

            showVerified: function (user) {
                return this.config.emailVerification && user.data.verified;
            },

            showRoles: function (user) {
                return Vue
                    .filter('toArray')(user.roles)
                    .filter(function (role) {
                        return role.id != 2;
                    })
                    .map(function (role) {
                        return role.name;
                    })
                    .join(', ');
            },

            load: function (page) {

                page = page !== undefined ? page : this.config.page;

                this.resource.query({ filter: this.config.filter, page: page }, function (data) {
                    vm.$set('users', data.users);
                    vm.$set('pages', data.pages);
                    vm.$set('config.page', page);
                    vm.$set('selected', []);
                });
            },

            getSelected: function() {
                return this.users.filter(function(user) {
                    return vm.selected.indexOf(user.id.toString()) !== -1;
                });
            }

        }

    });

});
