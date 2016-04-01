<template>

    <div>

        <div class="uk-panel-badge" v-if="!type.disableToolbar">
            <ul class="uk-subnav pk-subnav-icon">
                <li v-show="type.editable !== false && !editing">
                    <a class="pk-icon-edit pk-icon-hover uk-hidden" :title="'Edit' | trans" data-uk-tooltip="{delay: 500}" @click.prevent="edit"></a>
                </li>
                <li v-show="!editing">
                    <a class="pk-icon-handle pk-icon-hover uk-hidden uk-sortable-handle" :title="'Drag' | trans" data-uk-tooltip="{delay: 500}"></a>
                </li>
                <li v-show="editing">
                    <a class="pk-icon-delete pk-icon-hover" :title="'Delete' | trans" data-uk-tooltip="{delay: 500}" @click.prevent="remove" v-confirm="'Delete widget?'"></a>
                </li>
                <li v-show="editing">
                    <a class="pk-icon-check pk-icon-hover" :title="'Close' | trans" data-uk-tooltip="{delay: 500}" @click.prevent="save"></a>
                </li>
            </ul>
        </div>

        <component :is="type.component" :widget="widget" :editing.sync="editing"></component>

    </div>

</template>

<script>

module.exports = {

    props: {'widget': {}, 'editing': {default: false}},

    created: function () {
        this.$options.components = this.$parent.$options.components;
    },

    computed: {

        type: function () {
            return this.$root.getType(this.widget.type);
        }

    },

    methods: {

        edit: function () {
            this.$set('editing', true);
        },

        save: function () {
            this.$root.save(this.widget);
            this.$set('editing', false);
        },

        remove: function () {
            this.$root.remove(this.widget);
        }

    }

};

</script>
