<template>

    <div class="uk-form-select pk-filter" :class="{'uk-active': value }">
        <span>{{ label }}</span>
        <select v-if="isNumber" v-model="value" number>
            <template v-for="option in list">
                <optgroup :label="option.label" v-if="option.label">
                    <option v-for="opt in option.options" :value="opt.value">{{ opt.text }}</option>
                </optgroup>
                <option :value="option.value" v-else>{{ option.text }}</option>
            </template>
        </select>
        <select v-else v-model="value">
            <template v-for="option in list">
                <optgroup :label="option.label" v-if="option.label">
                    <option v-for="opt in option.options" :value="opt.value">{{ opt.text }}</option>
                </optgroup>
                <option :value="option.value" v-else>{{ option.text }}</option>
            </template>
        </select>
    </div>

</template>

<script>

    module.exports = {

        props: ['title', 'value', 'options', 'number'],

        created: function () {
            if (this.value === undefined) {
                this.value = '';
            }
        },

        computed: {

            isNumber: function() {
                return this.number !== undefined;
            },

            list: function() {
                return [{value: '', text: this.title }].concat(this.options);
            },

            label: function () {
                var list = this.list.concat(_.flatten(_.pluck(this.list, 'options')));
                var value = _.find(list, 'value', this.value);
                return value ? value.text : this.title;
            }

        }

    };

</script>
