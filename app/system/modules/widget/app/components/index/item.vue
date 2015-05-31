<template>

    <li class="uk-nestable-item" data-id="{{ widget.id }}" v-show="widget | showWidget">

        <div class="uk-nestable-panel pk-table-fake">

            <div class="pk-table-width-minimum"><input type="checkbox" name="id" value="{{ widget.id }}"></div>
            <div class="pk-table-min-width-100">
                <a v-if="type" v-on="click: edit(widget)">{{ widget.title }}</a>
                <span v-if="!type">{{ widget.title }}</span>
            </div>
            <div class="pk-table-width-150">
                <div class="uk-form-select uk-nestable-nodrag" v-el="select">
                    <a></a>
                    <select class="uk-width-1-1" v-model="position.id" v-on="input: reassign" options="positionOptions"></select>
                </div>
            </div>
            <div class="pk-table-width-150">{{ typeName }}</div>

        </div>

    </li>

</template>

<script>

    module.exports = {

        inherit: true,
        replace: true,

        ready: function() {
            UIkit.formSelect(this.$$.select, { target: 'a' });
        },

        computed: {

            type: function() {
                return _.find(this.config.types, { id: this.widget.type });
            },

            typeName: function() {
                return this.type ? this.type.name : this.$trans('Extension not loaded');
            }

        },

        methods: {

            reassign: function(e) {

                e.preventDefault();
                e.stopPropagation();

                this.reorder(e.target.value, _.find(this.positions, {id : e.target.value }).widgets.concat(this.widget))

            }

        }

    };

</script>
