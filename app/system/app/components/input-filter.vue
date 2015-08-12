<template>

    <div class="uk-form-select pk-filter">
        <span>{{ label }}</span>
        <select v-model="value" options="list"></select>
    </div>

</template>

<script>

    module.exports = {

        props: ['title', 'value', 'options'],

        computed: {

            list: function() {
                return [{value: '', text: this.title }].concat(this.options);
            },

            label: function () {
                var list = this.list.concat(_.flatten(_.pluck(this.list, 'options')));
                var label = (_.find(list, 'value', this.value) || _.find(list, 'value', parseInt(this.value)) || {}).text;
                return label ? label : this.title;
            }

        }

    };

</script>
