module.exports = {

    mixins: [
        require('../../lib/permissions')
    ],

    data: {
        role: {},
        config: window.$config
    },

    ready: function () {

        $(this.$el).on('change.uk.sortable', this.reorder);

    },

    computed: {

        current: function () {
            return _.find(this.roles, 'id', this.config.role) || this.roles[0];
        }

    },

    methods: {

        edit: function (role) {
            this.$set('role', $.extend({}, role));
            this.$.modal.open();
        },

        save: function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (!this.role) {
                return;
            }

            this.Roles.save({ id: this.role.id }, { role: this.role }, function (data) {

                if (this.role.id) {

                    var role = _.findIndex(this.roles, 'id', this.role.id);
                    this.roles.splice(role, 1, data.role);

                    this.$notify('Role saved');
                } else {
                    this.roles.push(data.role);
                    this.$notify('Role added');
                }

            }, function (data) {
                this.$notify(data, 'danger');
            });

            this.$.modal.close();
        },

        remove: function (role) {

            this.Roles.remove({ id: role.id }, function () {
                this.roles.splice(_.findIndex(this.roles, { id: role.id }), 1);
            });
        },

        reorder: function (e, sortable) {

            if (!sortable) {
                return;
            }

            var children = sortable.element.children();

            this.$.ordered.forEach(function (model) {
                model.role.priority = children.index(model.$el);
            });

            this.Roles.save({ id: 'bulk' }, { roles: this.roles }, function (data) {
                // this.$notify('Roles reordered.');
            }, function (data) {
                this.$notify(data, 'danger');
            });
        }

    }

};

$(function () {

    new Vue(module.exports).$mount('#roles');

});
