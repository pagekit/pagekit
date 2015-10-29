module.exports = {

    ready: function () {

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

        }, function () {

            this.editor = UIkit.htmleditor(this.$el, _.extend({ marked: window.marked, CodeMirror: window.CodeMirror }, this.options));

            this.editor.element
                .on('htmleditor-save', function (e, editor) {
                    if (editor.element[0].form) {
                        var event = document.createEvent('HTMLEvents');
                        event.initEvent('submit', true, false);
                        editor.element[0].form.dispatchEvent(event);
                    }
                });

            this.$watch('value', function (value) {
                if (value != this.editor.editor.getValue()) {
                    this.editor.editor.setValue(value);
                }
            });

            this.$watch('options.markdown', function (markdown) {
                this.editor.trigger(markdown ? 'enableMarkdown' : 'disableMarkdown');
            });

            this.$emit('ready');
        });

    }

};
