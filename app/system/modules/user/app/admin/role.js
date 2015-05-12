(function ($, UIkit) {

    var Component = Vue.extend({

        data: function () {
            return $data;
        },

        resource: null,

        created: function () {

            this.resource = this.$resource('api/user/role/:id');
            this.$watch('roles', UIkit.Utils.debounce(this.save, 1000), true);

        },

        computed: {

            rolesArray: function () {
                return Vue.filter('toArray')(this.roles);
            },

            authenticated: function () {
                return this.rolesArray.filter(function (role) {
                    return role.isAuthenticated;
                })[0]
            }

        },

        methods: {

            save: function () {
                this.resource.save({ id: 'bulk' }, { roles: this.roles }, function (data) {
                    if (!data.error) {
                        UIkit.notify(this.$trans('Roles saved'));
                    } else {
                        UIkit.notify(this.$trans('Failed to save roles.'), 'danger');
                    }
                }.bind(this));
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
                return role.isAdministrator || (this.isInherited(role, permission) && !this.hasPermission(role, permission))
            }

        }

    });

    $(function () {

        if (document.getElementById('js-permission')) {

            new Component({ el: '#js-permission' });

        } else {

            new Component({

                el: '#js-role',

                data: {
                    role: {},
                    config: $config
                },

                ready: function () {

                    $(this.$el).on('change.uk.sortable', this.reorder);
                    this.modal = UIkit.modal('#modal-role');

                },

                computed: {

                    current: function () {
                        return this.roles[this.config.role] || this.rolesArray[0];
                    }

                },

                methods: {

                    edit: function (role) {
                        this.$set('role', $.extend({}, role));
                        this.modal.show();
                    },

                    update: function (e) {

                        e.preventDefault();

                        if (!this.role) return;

                        this.modal.hide();

                        this.resource.save({ id: this.role.id }, { role: this.role }, function (data) {

                            if (this.roles[data.id]) {
                                this.roles[data.id] = data;
                            } else {
                                this.roles.$add(data.id, data);
                            }

                        }.bind(this));

                    },

                    remove: function (role) {
                        var self = this;
                        UIkit.modal.confirm(this.$trans('Are you sure?'), function () {
                            self.resource.remove({ id: role.id }, function () {
                                self.roles.$delete(role.id.toString());
                            });
                        });
                    },

                    reorder: function (e, sortable) {

                        if (!sortable) return;

                        var children = sortable.element.children();
                        this.$.ordered.forEach(function (model) {
                            model.role.priority = children.index(model.$el);
                        });
                    }

                }

            });

        }

    });

})(jQuery, UIkit);
