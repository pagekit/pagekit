<template>

    <textarea autocomplete="off" v-ref="editor" v-el="editor" v-model="value"></textarea>

</template>

<script>

    var Editor = Vue.extend({

        paramAttributes: ['type', 'value', 'options'],

        created: function() {

            var vm = this;

            _.each(this.$options.components, function (component) {

                if (component.options.plugin) {
                    vm.$addChild({

                        inherit: true,

                        created: function() {
                            Vue.nextTick(this.init.bind(this));
                        }

                    }, component);
                }

            });

        },

        compiled: function() {

            if (this.$el.hasAttributes()) {

                var attrs = this.$el.attributes;

                for (var i = attrs.length - 1; i >= 0; i--) {
                    this.$$.editor.setAttribute(attrs[i].name, attrs[i].value);
                    this.$el.removeAttribute(attrs[i].name);
                }

            }

            if (!this.$options.components[this.type]) {
                this.type = this.$options.components[window.$pagekit.editor] ? window.$pagekit.editor : 'textarea';
            }

            this.$addChild({ el: this.$$.editor, inherit: true }, this.$options.components[this.type]);
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
