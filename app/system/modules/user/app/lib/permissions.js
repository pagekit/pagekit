module.exports = {

    data: function () {
        return window.$data;
    },

    created: function () {

        var vm = this;

        this.Roles = this.$resource('api/user/role/:id');

        this.debounced = [];
        this.saveCb = Vue.util.debounce(function(role) {
            vm.$resource('api/user/role/:id').save({ id: 'bulk' }, { roles: vm.debounced }, function () {
                UIkit.notify(this.$trans('Permissions saved'));
            });
            vm.debounced = [];
        }, 1000);

    },

    computed: {

        authenticated: function () {
            return this.roles.filter(function (role) {
                return role.isAuthenticated;
            })[0];
        }

    },

    methods: {

        savePermissions: function(role) {

            if (!_.find(this.debounced, 'id', role.id)) {
                this.debounced.push(role);
            }

            this.saveCb();
        },

        addPermission: function (role, permission) {
            return !role.isAdministrator ? role.permissions.push(permission) : null;
        },

        hasPermission: function (role, permission) {
            return -1 !== role.permissions.indexOf(permission);
        },

        isInherited: function (role, permission) {
            return !role.isLocked && this.hasPermission(this.authenticated, permission);
        },

        showFakeCheckbox: function (role, permission) {
            return role.isAdministrator || (this.isInherited(role, permission) && !this.hasPermission(role, permission));
        }

    }

};
