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

            User = this.$resource(this.config.urls.user);

            this.$watch('user.status', function (status) {
                if (typeof status === 'string') {
                    vm.user.status = parseInt(status);
                }
            });

        },

        computed: {

            gravatar: function () {
                return gravatar(this.user.email, { size: 300, backup: 'mm', rating: 'g' });
            },

            loginText: function () {
                return this.$trans('Last login: %date%', { date: this.user.login ? this.$date('medium', this.user.login) : this.$trans('Never') });
            },

            registeredText: function () {
                return this.$trans('Registered since: %date%', { date: this.$date('medium', this.user.registered) });
            }

        },

        methods: {

            save: function (e) {
                e.preventDefault();

                var roles = this.roles.filter(function (role) { return role.selected; }).map(function (role) { return role.id; });

                User.save({ user: this.user, password: this.password, roles: roles, id: this.user.id }, function (data) {

                    if (data.user) {
                        vm.$set('user', data.user);
                    }

                    UIkit.notify(data.message || data.error, data.error ? 'danger' : 'success');
                });
            }

        }

    });

});
