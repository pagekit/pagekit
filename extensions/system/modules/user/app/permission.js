jQuery(function ($) {

    var Role, vm = new Vue({

        el: '#js-permission',

        data: {
            roles: permission.data.roles,
            permissions: permission.data.permissions
        },

        ready: function () {

            Role = this.$resource(this.$url('api/system/role')+'/:id');

            var save = UIkit.Utils.debounce(this.save, 1000);
            this.$watch('roles', function () {
                save();
            }, true);

        },

        methods: {

            getAuthenticatedRole: function () {
                return Vue.filter('toArray')(this.roles).filter(function(role) { return role.isAuthenticated; })[0];
            },

            addPermission: function(role, permission) {
                return !role.isAdministrator ? role.permissions.push(permission) : null;
            },

            hasPermission: function(role, permission) {
                return -1 !== role.permissions.indexOf(permission);
            },

            isInherited: function(role, permission) {
                return !role.isLocked && this.hasPermission(this.getAuthenticatedRole(), permission);
            },

            showFakeCheckbox: function(role, permission) {
                return role.isAdministrator || (this.isInherited(role, permission) && !this.hasPermission(role, permission))
            },

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
