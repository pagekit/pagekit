<template>
    <div :class="classes">
        <label type="button" class="uk-button" v-for="option in options" :class="getButtonClass($index)"><input type="checkbox" style="display: none;" v-model="field" :value="option.value" :disabled="disabled || option.disabled" /> {{ option.label | trans }}</label>
    </div>
</template>
<script>
    module.exports = {

        props: {
            field: {
                type: [Array],
                twoWay: true
            },
            options: [Array, Object],
            modifier: [String, Function],
            button: {
                type: [String, Function],
                default: ''
            },
            active: [String, Function],
            disabled: {
                type: Boolean,
                default: false
            }
        },

        ready: function () {

        },

        methods: {
            getButtonClass: function (index) {
                var button_class = this.button.split(' ');

                if (this.field.indexOf(this.options[index].value) !== -1) {
                    button_class.push('uk-button');
                    button_class.push('uk-active');
                    if(this.active) {
                        button_class.push(this.active);
                    }
                } else {
                    button_class.push('uk-button');
                }

                return button_class;
            }
        },

        computed: {
            classes: function () {
                var classes = this.modifier.split(' ');

                classes.push('uk-button-group')

                return classes;
            }
        }
    };

    Vue.component('button-checkbox', function (resolve) {
        resolve(module.exports);
    });
</script>