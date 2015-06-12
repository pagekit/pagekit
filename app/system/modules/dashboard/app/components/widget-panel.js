module.exports = {

    replace: true,
    inherit: true,

    ready: function () {

        if (this.type.editable !== false) {
            this.$watch('widget', Vue.util.debounce(this.save, 500), true, false);
        }

    },

    computed: {

        type: function () {
            return this.getType(this.widget.type);
        },

        component: function () {
            return this.type.component;

        },

        isEditing: function () {
            return !!this.editing[this.widget.id];
        }

    },

    methods: {

        edit: function (force) {

            var id = this.widget.id;

            if (!force && this.editing[id]) {
                this.editing.$delete(id);
            } else {
                this.editing.$set(id, true);
            }

        },

        save: function () {

            var data = { widget: this.widget };

            this.$broadcast('save', data);

            this.Widgets.save({ id: this.widget.id }, data);

        },

        remove: function () {

            var id = this.widget.id;

            this.Widgets.delete({ id: id }, function () {
                this.widgets.splice(_.findIndex(this.widgets, { id: id }), 1);
            });
        }

    }

};
