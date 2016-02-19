<template>

    <textarea autocomplete="off" :style="{height: height + 'px'}" class="uk-invisible" v-el:editor v-model="value"></textarea>

</template>
<script>

    module.exports = {

        props: ['value', 'height'],

        ready: function () {

            this.height += 47;

            this.$asset({

                css: [
                    'app/assets/codemirror/hint.css',
                    'app/assets/codemirror/codemirror.css'
                ],
                js: [
                    'app/assets/codemirror/codemirror.js',
                    'app/assets/marked/marked.js',
                    'app/assets/uikit/js/components/htmleditor.min.js'
                ]

            }).then(function () {

                var editor = this.editor = UIkit.htmleditor(this.$els.editor, _.extend({
                    marked: window.marked,
                    CodeMirror: window.CodeMirror
                }, this.$parent.options));

                editor.element
                    .on('htmleditor-save', function (e, editor) {
                        if (editor.element[0].form) {
                            var event = document.createEvent('HTMLEvents');
                            event.initEvent('submit', true, false);
                            editor.element[0].form.dispatchEvent(event);
                        }
                    });

                this.$watch('value', function (value) {
                    if (value != editor.editor.getValue()) {
                        editor.editor.setValue(value);
                    }
                });

                this.$watch('$parent.options.markdown', function (markdown) {
                        editor.trigger(markdown ? 'enableMarkdown' : 'disableMarkdown');
                    }, {immediate: true}
                );

                this.$dispatch('ready');

            })

        }

    };

</script>
