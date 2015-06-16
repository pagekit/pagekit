<template>

    <textarea autocomplete="off" v-el="editor" v-model="value"></textarea>

</template>

<script>

    var $ = require('jquery');
    var Vue = require('vue');

    var Editor = Vue.extend({

        props: ['type', 'value', 'options'],

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

            _.forIn(this.$options.components, function (component) {

                if (component.options.plugin) {
                    editor.$addChild({ inherit: true }, component);
                }

            });

        },

        components: {

            textarea: {},
            htmleditor: require('./editor-html'),
            image: require('./image'),
            video: require('./video'),
            url: require('./url')

        }

    });

    Editor.create = function (element, options) {
        return new Editor({ el: element, data: options});
    };

    Vue.component('v-editor', Editor);

    $(function () {
        $('[data-editor]').each(function () {
            Editor.create(this, $(this).data('editor'));
        });
    });

    module.exports = Editor;

</script>
