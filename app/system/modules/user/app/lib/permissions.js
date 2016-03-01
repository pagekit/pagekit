module.exports = {

    data: function () {
        return window.$data;
    },

    created: function () {

        this.Roles = this.$resource('api/user/role{/id}');

        this.debounced = [];

        this.saveCb = Vue.util.debounce(function() {
            this.Roles.save({ id: 'bulk' }, { roles: this.debounced }).then(function () {
                this.$notify('Permissions saved');
            });

            this.debounced = [];
        }.bind(this), 1000);

    },

    computed: {

        authenticated: function () {
            return this.roles.filter(function (role) {
                return role.authenticated;
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
            return !role.administrator ? role.permissions.push(permission) : null;
        },

        hasPermission: function (role, permission) {
            return -1 !== role.permissions.indexOf(permission);
        },

        isInherited: function (role, permission) {
            return !role.locked && this.hasPermission(this.authenticated, permission);
        },

        showFakeCheckbox: function (role, permission) {
            return role.administrator || (this.isInherited(role, permission) && !this.hasPermission(role, permission));
        }

    }

};
