<template>

    <textarea autocomplete="off" v-el="editor" v-model="value"></textarea>

</template>

<script>

    module.exports = {

        props: ['type', 'value', 'options'],

        compiled: function() {

            if (this.$el.hasAttributes()) {

                var attrs = this.$el.attributes;

                for (var i = attrs.length - 1; i >= 0; i--) {
                    this.$$.editor.setAttribute(attrs[i].name, attrs[i].value);
                    this.$el.removeAttribute(attrs[i].name);
                }

            }

            var components = this.$options.components, type = 'editor-'+this.type, self = this;
            this.editor = this.$addChild({ el: this.$$.editor, inherit: true }, components[type] || components['editor-'+window.$pagekit.editor] || components['editor-textarea']);

            this.$on('ready', function() {

                _.forIn(this.$options.components, function (component) {

                    if (component.options && component.options.plugin) {
                        this.editor.$addChild({ inherit: true }, component);
                    }

                }, this);

                return false;

            });
        },

        components: {

            'editor-textarea': {},
            'editor-html': require('./editor-html'),
            'editor-code': require('./editor-code'),
            'plugin-link': require('./link'),
            'plugin-image': require('./image'),
            'plugin-video': require('./video'),
            'plugin-url': require('./url')

        }

    };

    Vue.component('v-editor', function (resolve) {
        resolve(module.exports);
    });

</script>
