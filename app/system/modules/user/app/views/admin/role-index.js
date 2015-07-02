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
        this.modal = UIkit.modal('#modal-role');

    },

    computed: {

        current: function () {
            return _.find(this.roles, 'id', this.config.role) || this.roles[0];
        }

    },

    methods: {

        edit: function (role) {
            this.$set('role', $.extend({}, role));
            this.modal.show();
        },

        save: function (e) {
            e.preventDefault();
            e.stopPropagation();

            if (!this.role) {
                return;
            }

            this.modal.hide();

            this.Roles.save({ id: this.role.id }, { role: this.role }, function (data) {

                var role = _.findIndex(this.roles, 'id', this.role.id);

                if (role !== -1) {
                    this.roles.splice(role, 1, data);
                    UIkit.notify(this.$trans('Role saved'));
                } else {
                    this.roles.push(data);
                    UIkit.notify(this.$trans('Role added'));
                }

            });
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
                if (!data.error) {
                    UIkit.notify(this.$trans('Roles saved'));
                } else {
                    UIkit.notify(this.$trans('Failed to save roles.'), 'danger');
                }
            });
        }

    }

};

$(function () {

    new Vue(module.exports).$mount('#roles');

});
