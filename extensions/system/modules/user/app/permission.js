jQuery(function ($) {

    var Role, vm = new Vue({

        el: '#js-permission',

        data: {
            config: permission.config,
            roles: permission.data.roles,
            permissions: permission.data.permissions
        },

        ready: function () {

            Role = this.$resource(this.config.urls.role+'/:id');

            var save = UIkit.Utils.debounce(this.save, 1000);
            this.$watch('roles', function () {
                save();
            }, true);

        },

        methods: {
            save: function () {

                var self = this;

                Role.save({ id: 'bulk' }, { roles: this.roles }, function (data) {
                    if (!data.error) {
                        UIkit.notify(self.$trans('Permissions saved'), 'success');
                    } else {
                        UIkit.notify(self.$trans('Failed to save permissions.'), 'danger');
                    }
                });
            }
        }

    });

});
