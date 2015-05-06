<template>

    <div class="uk-nestable-item pk-table-fake">

        <div class="pk-table-width-minimum">
            <div class="uk-nestable-handle">​</div>
        </div>
        <div class="pk-table-width-minimum"><input type="checkbox" name="id" value="{{ widget.id }}"></div>
        <div class="pk-table-min-width-100">
            <a v-show="type" v-on="click: edit(widget)">{{ widget.title }}</a>
            <span v-show="!type">{{ widget.title }}</span>
        </div>
        <div class="pk-table-width-150">
            <div class="uk-form-select" v-el="select">
                <a></a>
                <select v-model="position.id" class="uk-width-1-1" options="positionOptions" v-on="input: reassign"></select>
            </div>
        </div>
        <div class="pk-table-width-150">{{ typeName }}</div>

    </div>

</template>

<script>

    module.exports = {

        inherit: true,

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
