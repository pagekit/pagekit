jQuery(function ($) {

    var Role, modal, vm = new Vue({

        el: '#js-role',

        data: {
            current: {},
            role: {},
            config: role.config,
            roles: role.data.roles,
            permissions: role.data.permissions
        },

        ready: function () {

            Role = this.$resource(this.$url('api/system/role')+'/:id');

            this.config.role = this.config.role || this.sorted[0].id;

            this.setCurrent(this.roles[this.config.role] || {});

            this.$watch('roles', UIkit.Utils.debounce(this.save, 1000), true);

            $(this.$el).on('change.uk.sortable', this.reorder);

        },

        computed: {

            sorted: function() {
                return Vue.filter('toArray')(this.roles);
            }

        },

        methods: {

            setCurrent: function(role) {
                this.$set('current', role);
            },

            edit: function(role) {

                if (!modal) {
                    modal = UIkit.modal('#modal-role');
                }

                this.$set('role', $.extend({}, role));
                modal.show();

            },

            update: function(e) {

                e.preventDefault();

                if (!this.role) return;

                modal.hide();
                Role.save({ id: this.role.id }, { role: this.role }, function (data) {

                    if (vm.roles[data.id]) {
                        vm.roles[data.id] = data;
                    } else {
                        vm.roles.$add(data.id, data);
                    }

                });

            },

            remove: function(role) {
                UIkit.modal.confirm(this.$trans('Are you sure?'), function() {
                    Role.remove({ id: role.id }, function () {
                        vm.roles.$delete(role.id.toString());
                    });
                });
            },

            reorder: function(e, sortable) {

                if (!sortable) return;

                var children = sortable.element.children();
                vm.$.ordered.forEach(function(model) {
                    model.role.priority = children.index(model.$el);
                });
            },

            save: function() {
                Role.save({ id: 'bulk' }, { roles: this.roles }, function (data) {
                    if (!data.error) {
                        UIkit.notify(vm.$trans('Roles saved'), 'success');
                    } else {
                        UIkit.notify(vm.$trans('Failed to save permissions.'), 'danger');
                    }
                });
            },

            getAuthenticatedRole: function () {
                return this.sorted.filter(function(role) { return role.isAuthenticated; })[0];
            },

            addPermission: function(role, permission) {
                return !role.isAdministrator ? role.permissions.push(permission) : null;
            },

            hasPermission: function(role, permission) {
                return role.permissions && -1 !== role.permissions.indexOf(permission);
            },

            isInherited: function(role, permission) {
                return !role.isLocked && this.hasPermission(this.getAuthenticatedRole(), permission);
            },

            showFakeCheckbox: function(role, permission) {
                return role.isAdministrator || (this.isInherited(role, permission) && !this.hasPermission(role, permission))
            }
        }

    });

});
