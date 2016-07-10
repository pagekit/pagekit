<template>
    <div :class="modifier">
        <label type="button" class="uk-button" v-for="option in options" :class="getButtonClass($index)"><input type="radio" style="display: none;" v-model="field" :value="option.value" :disabled="disabled || option.disabled" /> {{ option.label | trans }}</label>
    </div>
</template>
<script>
    module.exports = {

        props: {
            field: {
                type: [String, Number],
                twoWay: true
            },
            options: [Object, Array],
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

                if (this.options[index].value == this.field) {
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
        }
    };

    Vue.component('button-radio', function (resolve) {
        resolve(module.exports);
    });
</script>