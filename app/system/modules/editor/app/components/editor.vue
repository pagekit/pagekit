<template>

    <textarea autocomplete="off" v-el="editor" v-model="value"></textarea>

</template>

<script>

    var Editor = Vue.extend({

        paramAttributes: ['type', 'value', 'options'],

        compiled: function() {

            if (this.$el.hasAttributes()) {

                var attrs = this.$el.attributes;

                for (var i = attrs.length - 1; i >= 0; i--) {
                    this.$$.editor.setAttribute(attrs[i].name, attrs[i].value);
                    this.$el.removeAttribute(attrs[i].name);
                }

            }

            var components = this.$options.components;
            this.editor = this.$addChild({ el: this.$$.editor, inherit: true }, components[this.type] || components[window.$pagekit.editor] || components['textarea']);
        },

        ready: function() {

            var editor = this.editor;

            _.each(this.$options.components, function (component) {

                if (component.options.plugin) {
                    editor.$addChild({ inherit: true }, component);
                }

            });

        },

        components: {

            textarea: {},
            htmleditor: require('./htmleditor'),
            image: require('./image/image'),
            video: require('./video/video'),
            url: require('./url')

        }

    });

    Editor.create = function (element, options) {
        return new Editor.extend({ el: element, data: options});
    };

    module.exports = Editor;

</script>
