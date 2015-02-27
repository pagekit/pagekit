jQuery(function ($) {

    var User, vm = new Vue({

        el: '#js-user',

        data: {
            config     : user.config,
            users      : user.data.users.users,
            pages      : user.data.users.pages,
            roles      : user.data.roles,
            statuses   : user.data.statuses,
            permissions: user.data.permissions,
            selected   : []
        },

        ready: function () {

            var vm = this;

            User = this.$resource(this.config.urls.user+'/:id');

            this.config.filter = $.extend({ status: '', role: '', permission: '' }, this.config.filter ? this.config.filter : {});

            this.statuses = [{ text: this.$trans('- Status -'), value: '' }, { text: this.$trans('New'), value: 'new' }];
            $.each(user.data.statuses, function (id, status) {
                vm.statuses.push({ text: status, value: id });
            });

            this.roles = [{ text: this.$trans('- Role -'), value: '' }];
            $.each(user.data.roles, function (id, role) {
                vm.roles.push({ text: role.name, value: id });
            });

            this.permissions = [{ text: this.$trans('- Permission -'), value: '' }];
            $.each(user.data.permissions, function (group, permissions) {
                var options = [];
                $.each(permissions, function (id, permission) {
                    options.push({ text: permission.title, value: id });
                });
                vm.permissions.push({ label: group, options: options });
            });

            this.$watch('config.page', function (page) {
                vm.updateUsers(page);
            }, true);

            this.$watch('config.filter', function () {
                vm.updateUsers(0);
            }, true);
        },

        methods: {

            save: function (user) {
                User.save({ id: user.id }, { id: user.id, user: user }, function (data) {
                    vm.updateUsers(vm.config.page);
                    UIkit.notify(data.message || data.error, data.error ? 'danger' : 'success');
                });
            },

            status: function(status) {

                var users = this.getSelected();

                users.forEach(function(user) {
                    user.status = status;
                });

                User.save({ id: 'bulk' }, { users: users }, function (data) {
                    vm.updateUsers(vm.config.page);
                    UIkit.notify(data.message || data.error, data.error ? 'danger' : 'success');
                });
            },

            remove: function() {
                User.delete({ id: 'bulk' }, { ids: this.selected }, function (data) {
                    vm.updateUsers(vm.config.page);
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

            updateUsers: function (page) {
                User.query({ filter: this.config.filter, page: page }, function (data) {

                    if (data.users) {
                        vm.$set('users', data.users);
                    }

                    if (data.pages) {
                        vm.pages = data.pages;
                    }

                    vm.config.page = page;
                    vm.selected = [];
                });
            },

            getSelected: function() {
                return this.users.filter(function(user) { return -1 !== vm.selected.indexOf(user.id.toString()) });
            }

        }

    });

});
