jQuery(function ($) {

    var Role, vm = new Vue({

        el: '#js-permission',

        data: {
            roles: permission.data.roles,
            permissions: permission.data.permissions
        },

        ready: function () {

            Role = this.$resource('api/system/role/:id');

            this.$watch('roles', UIkit.Utils.debounce(this.save, 1000), true);

        },

        computed: {

            sorted: function() {
                return Vue.filter('toArray')(this.roles);
            }

        },

        methods: {

            getAuthenticatedRole: function () {
                return this.sorted.filter(function(role) { return role.isAuthenticated; })[0];
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
                Role.save({ id: 'bulk' }, { roles: this.roles }, function (data) {
                    if (!data.error) {
                        UIkit.notify(vm.$trans('Permissions saved'), 'success');
                    } else {
                        UIkit.notify(vm.$trans('Failed to save permissions.'), 'danger');
                    }
                });
            }
        }

    });

});
