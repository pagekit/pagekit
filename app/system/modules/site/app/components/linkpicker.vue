<template>

    <div class="uk-form-row">
        <label for="form-linkpicker-url" class="uk-form-label">{{ 'Url' | trans }}</label>

        <div class="uk-form-controls">
            <input id="form-linkpicker-url" class="uk-form-width-large" type="text" v-model="url">
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-style" class="uk-form-label">{{ 'Type' | trans }}</label>

        <div class="uk-form-controls">
            <select id="form-style" class="uk-form-width-large" v-model="type" options="types | orderBy 'name'"></select>
        </div>
    </div>

    <div v-component="{{ type }}" url="{{@ url }}" v-if="type"></div>

</template>

<script>

    var Linkpicker = Vue.extend({

        props: {

            url: {
                type: String,
                default: 'http://'
            }

        },

        data: function() {
            return {
                type: false,
                url: ''
            };
        },

        computed: {

            types: function() {

                var types = [{ text: this.$trans('- Select Type -'), value: '' }];

                _.forIn(this.$options.components, function (component) {

                    if (component.options.link) {
                        types.push({ text: component.options.link.label, value: component.options.link.name });
                    }

                });

                return types;
            }

        }

    });

    module.exports = Linkpicker;

</script>
