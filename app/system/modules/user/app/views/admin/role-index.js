module.exports = {

    el: '#roles',

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
            this.$set('role', $.extend({}, role || {}));
            this.$refs.modal.open();
        },

        save: function () {
            if (!this.role) {
                return;
            }

            this.Roles.save({ id: this.role.id }, { role: this.role }).then(function (res) {

                var data = res.data;

                if (this.role.id) {

                    var role = _.findIndex(this.roles, 'id', this.role.id);
                    this.roles.splice(role, 1, data.role);

                    this.$notify('Role saved');
                } else {
                    this.roles.push(data.role);
                    this.$notify('Role added');
                }

            }, function (res) {
                this.$notify(res.data, 'danger');
            });

            this.$refs.modal.close();
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

            sortable.element.children().each(function(i) {
                this.__v_frag.raw.priority = i;
            });

            this.Roles.save({ id: 'bulk' }, { roles: this.roles }, function () {
                this.$notify('Roles reordered.');
            }, function (data) {
                this.$notify(data, 'danger');
            });
        }

    }

};

Vue.ready(module.exports);
