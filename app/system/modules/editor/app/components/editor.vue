<template>

    <div>
        <textarea autocomplete="off" :style="{height: height + 'px'}" :class="{'uk-invisible': !show}" v-el:editor v-model="value"></textarea>
    </div>

</template>

<script>

    module.exports = {

        props: ['type', 'value', 'options'],

        data: function () {
            return {
                editor: {},
                height: 500
            }
        },

        compiled: function () {

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

            var components = this.$options.components, type = 'editor-' + this.type, self = this,
                Editor = components[type] || components['editor-' + window.$pagekit.editor] || components['editor-textarea'];

            new Editor({parent: this}).$on('ready', function () {

                _.forIn(self.$options.components, function (Component) {
                    if (Component.options && Component.options.plugin) {
                        new Component({parent: self});
                    }
                }, this);

            });
        },

        components: {

            'editor-textarea': {

                created: function () {
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

        },

        utils: {
            'image-picker': Vue.extend(require('./image-picker.vue')),
            'video-picker': Vue.extend(require('./video-picker.vue')),
            'link-picker': Vue.extend(require('./link-picker.vue'))
        }

    };

    Vue.component('v-editor', function (resolve) {
        resolve(module.exports);
    });

</script>
