<template>
    <textarea autocomplete="off" :style="{height: height + 'px'}" class="uk-invisible" v-el:editor v-model="value"></textarea>
</template>

<script>

    module.exports = {

        props: ['value', 'height'],

        created: function () {

            var self = this, $el = $(this.$els.editor), $parent = $el.parent();

            $parent.addClass('pk-editor');

            this.$asset({
                css: [
                    'app/assets/codemirror/hint.css',
                    'app/assets/codemirror/codemirror.css'
                ],
                js: [
                    'app/assets/codemirror/codemirror.js'
                ]

            }).then(function () {

                this.editor = CodeMirror.fromTextArea(this.$els.editor, _.extend({
                    mode: 'htmlmixed',
                    dragDrop: false,
                    autoCloseTags: true,
                    matchTags: true,
                    autoCloseBrackets: true,
                    matchBrackets: true,
                    indentUnit: 4,
                    indentWithTabs: false,
                    tabSize: 4
                }, this.$parent.options));

                $parent.attr('data-uk-check-display', 'true').on('display.uk.check', function (e) {
                    self.editor.refresh();
                });

                this.editor.on('change', function () {
                    self.editor.save();
                    self.value = self.editor.getValue();

                    $el.trigger('input');
                });

                this.$watch('value', function (value) {
                    if (value != this.editor.getValue()) {
                        this.editor.setValue(value);
                    }
                    this.editor.refresh();
                }, {immediate: true});
                this.editor.refresh();
                this.$emit('ready');

            });
        }

    };

</script>
