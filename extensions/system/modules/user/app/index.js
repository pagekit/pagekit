jQuery(function ($) {

    var vm = new Vue({

        el: '#js-user',

        data: {
            config     : user.config,
            users      : user.data.users.users,
            pages      : user.data.users.pages,
            roles      : user.data.roles,
            statuses   : user.data.statuses,
            permissions: user.data.permissions
        },

        ready: function () {

            var vm = this;

            this.config.filter = $.extend({ status: '', role: '', permission: '' }, this.config.filter ? this.config.filter : {});
            this.User = $.resource(this.config.urls.user);

            this.statuses = [{ text: this.$trans('- Status -'), value: '' }, { text: this.$trans('New'), value: 'new' }];
            $.each(user.data.statuses, function (id, status) {
                vm.statuses.push({ text: status, value: id })
            });

            this.roles = [{ text: this.$trans('- Role -'), value: '' }];
            $.each(user.data.roles, function (id, role) {
                vm.roles.push({ text: role.name, value: id })
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
                this.User.save({ id: user.id }, { id: user.id, user: user }, function (data) {
                    vm.updateUsers();
                    UIkit.notify(data.message || data.error, data.error ? 'danger' : 'success');
                });
            },

            toggleStatus: function (user) {
                user.status = !!user.status ? 0 : 1;
                this.save(user);
            },

            showGravatar: function (user) {
                return gravatar(user.email, { size: 80, backup: 'mm', rating: 'g' });
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
                this.User.query({ filter: this.config.filter, page: page }, function (data) {

                    if (data.users) {
                        vm.$set('users', data.users);
                    }

                    if (data.pages) {
                        vm.pages = data.pages;
                    }

                    vm.config.page = page;

                });
            }

        }

    });

});
