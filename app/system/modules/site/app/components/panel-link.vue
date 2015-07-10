<template>

    <div class="uk-form-row">
        <label for="form-style" class="uk-form-label">{{ 'Extension' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-style" class="uk-width-1-1" v-model="type" options="types"></select>
        </div>
    </div>

    <div v-component="{{ type }}" url="{{@ url }}" v-if="type"></div>

</template>

<script>

    module.exports = Vue.extend({

        data: function() {
            return {
                type: false,
                url: ''
            };
        },

        watch: {

            type: {
                handler: function(type) {
                    if (!type && this.types.length) {
                        this.type = this.types[0].value;
                    }
                },
                immediate: true
            }

        },

        computed: {

            types: function() {

                var types = [];

                _.forIn(this.$options.components, function (component) {

                    if (component.options.link) {
                        types.push({ text: component.options.link.label, value: component.options.link.name });
                    }

                });

                return _.sortBy(types, 'text');
            }

        }

    });

    Vue.component('panel-link', module.exports);

</script>
