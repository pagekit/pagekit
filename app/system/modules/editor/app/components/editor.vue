<template>

    <div>
        <textarea autocomplete="off" v-style="height: height + 'px'" v-class="uk-invisible: !show" v-el="editor" v-model="value"></textarea>
    </div>

</template>

<script>

    module.exports = {

        props: ['type', 'value', 'options'],

        compiled: function() {

            this.$set('height', this.options && this.options.height ? this.options.height : 500);

            if (this.$el.hasAttributes()) {

                var attrs = this.$el.attributes;

                for (var i = attrs.length - 1; i >= 0; i--) {
                    this.$$.editor.setAttribute(attrs[i].name, attrs[i].value);
                    this.$el.removeAttribute(attrs[i].name);
                }

            }

            var components = this.$options.components, type = 'editor-'+this.type, self = this;

            this
                .$addChild({ el: this.$$.editor, inherit: true }, components[type] || components['editor-'+window.$pagekit.editor] || components['editor-textarea'])
                .$on('ready', function() {

                    _.forIn(self.$options.components, function (component) {

                        if (component.options && component.options.plugin) {
                            this.$addChild({ inherit: true }, component);
                        }

                    }, this);

                });
        },

        components: {

            'editor-textarea': {

                ready: function() {
                    this.$emit('ready');
                    this.$parent.$set('show', true);
                }

            },
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
