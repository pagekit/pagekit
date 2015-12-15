<template>

    <div class="uk-form-row">
        <label for="form-style" class="uk-form-label">{{ 'Extension' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-style" class="uk-width-1-1" v-model="type">
                <option v-for="type in types" :value="type.value">{{ type.text }}</option>
            </select>
        </div>
    </div>

    <div :is="type" :link.sync="link" v-if="type"></div>

</template>

<script>

    window.Links = module.exports = {

        data: function () {
            return {
                type: false,
                link: ''
            };
        },

        watch: {

            type: {
                handler: function (type) {
                    if (!type && this.types.length) {
                        this.type = this.types[0].value;
                    }
                },
                immediate: true
            }

        },

        computed: {

            types: function () {

                var types = [], options;

                _.forIn(this.$options.components, function (component, name) {

                    options = component.options || {};

                    if (options.link) {
                        types.push({ text: options.link.label, value: name });
                    }

                });

                return _.sortBy(types, 'text');
            }

        },

        components: {}

    };

    Vue.component('panel-link', function (resolve) {
        resolve(module.exports);
    });

</script>
