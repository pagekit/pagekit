<template>

    <div class="uk-panel-badge" v-if="!type.disableToolbar">
        <ul class="uk-subnav pk-subnav-icon">
            <li v-show="editing[widget.id]">
                <a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: remove()" v-confirm="'Delete widget?'"></a>
            </li>
            <li v-show="type.editable !== false && !editing[widget.id]">
                <a class="pk-icon-edit pk-icon-hover uk-hidden" title="{{ 'Edit' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: edit()"></a>
            </li>
            <li v-show="type.editable !== false && editing[widget.id]">
                <a class="pk-icon-check pk-icon-hover" title="{{ 'Close' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: edit()"></a>
            </li>
        </ul>
    </div>

    <component is="{{ component }}" widget="{{ widget }}" editing="{{ isEditing }}"></component>

</template>

<script>

module.exports = {

    inherit: true,
    replace: false,

    ready: function () {

        if (this.type.editable !== false) {
            this.$watch('widget', Vue.util.debounce(this.save, 500), {deep: true});
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

</script>
