jQuery(function ($) {

    var User, vm = new Vue({

        el: '#js-user-edit',

        data: {
            config: user.config,
            user: user.data.user,
            roles: user.data.roles,
            statuses: user.data.statuses
        },

        ready: function () {

            User = this.$resource('api/system/user/:id');

            this.$watch('user.status', function (status) {
                if (typeof status === 'string') {
                    vm.user.status = parseInt(status);
                }
            });

        },

        methods: {

            save: function (e) {
                e.preventDefault();

                var roles = this.roles.filter(function (role) { return role.selected; }).map(function (role) { return role.id; });

                User.save({ id: this.user.id }, { user: this.user, password: this.password, roles: roles, id: this.user.id }, function (data) {

                    if (data.user) {
                        vm.$set('user', data.user);
                    }

                    UIkit.notify(data.message || data.error, data.error ? 'danger' : 'success');
                });
            }

        }

    });

});
