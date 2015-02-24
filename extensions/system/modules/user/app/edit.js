jQuery(function ($) {

    var vm = new Vue({

        el: '#js-user-edit',

        data: {
            config: user.config,
            user: user.data.user,
            roles: user.data.roles,
            statuses: user.data.statuses
        },

        ready: function () {

            this.$watch('user.status', function (status) {
                if ('string' === typeof status) {
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

                $.post(this.config.urls.user, { id: this.user.id, user: this.user, password: this.password, roles: roles }, function (data) {

                    if (data.user) {
                        vm.user = data.user;
                    }

                    UIkit.notify(data.message || data.error, data.error ? 'danger' : 'success');
                });
            }

        }

    });

});
