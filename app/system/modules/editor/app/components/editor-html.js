module.exports = {

    created: function () {

        this.$parent.$set('height', this.$parent.height + 47);

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

            var editor = this.$parent.editor = UIkit.htmleditor(this.$parent.$els.editor, _.extend({
                marked: window.marked,
                CodeMirror: window.CodeMirror
            }, this.$parent.options));

            editor.element
                .on('htmleditor-save', function (e, editor) {
                    if (editor.element[0].form) {
                        var event = document.createEvent('HTMLEvents');
                        event.initEvent('submit', true, true);
                        editor.element[0].form.dispatchEvent(event);
                    }
                });

            editor.on('render', function () {
                var regexp = /<script(.*)>[^<]+<\/script>|<style(.*)>[^<]+<\/style>/gi;
                editor.replaceInPreview(regexp, '');
            });

            this.$watch('$parent.value', function (value) {
                if (value != editor.editor.getValue()) {
                    editor.editor.setValue(value);
                }
            });

            this.$watch('$parent.options.markdown', function (markdown) {
                    editor.trigger(markdown ? 'enableMarkdown' : 'disableMarkdown');
                }, {immediate: true}
            );

            this.$emit('ready');
        })

    }

};
