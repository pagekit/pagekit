<template>

    <div>
        <component :is="type" :value.sync="value" :height.sync="height" v-ref:editor></component>
    </div>

</template>

<script>

    module.exports = {

        props: ['type', 'value', 'options'],

        data: function () {
            return {
                editor: '',
                height: 500
            }
        },

        created: function () {

            var components = this.$options.components;

            if (components['editor-' + this.type]) {
                this.type = 'editor-' + this.type;
            } else if (components['editor-' + window.$pagekit.editor]) {
                this.type = 'editor-' + window.$pagekit.editor;
            } else {
                this.type = 'editor-textarea';
            }

        },

        compiled: function () {

            var vm = this;

            this.$on('ready', function () {
                _.forIn(this.$options.components, function (Component) {
                    if (Component.options && Component.options.plugin) {
                        new Component({parent: vm.$refs.editor});
                    }
                }, this);
            });

        },

        ready: function () {

            if (this.options && this.options.height) {
                this.height = this.options.height
            }

            if (this.$el.hasAttributes()) {

                var attrs = this.$el.attributes;

                for (var i = attrs.length - 1; i >= 0; i--) {
                    this.$els.editor.setAttribute(attrs[i].name, attrs[i].value);
                    this.$el.removeAttribute(attrs[i].name);
                }

            }

        },

        components: {

            'plugin-link': require('./html/link'),
            'plugin-image': require('./html/image'),
            'plugin-video': require('./html/video'),
            'plugin-url': require('./html/url'),
            'editor-html': require('./editor-html.vue'),
            'editor-code': require('./editor-code.vue'),
            'editor-textarea': {

                props: ['value', 'height'],

                template: '<textarea autocomplete="off" :style="{height: height + \'px\'}" v-model="value"></textarea>'

            }

        }

    };

    Vue.component('v-editor', function (resolve) {
        resolve(module.exports);
    });

</script>
