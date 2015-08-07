module.exports = {

    ready: function () {

        this.$asset({

            css: [
                'vendor/assets/codemirror/hint.css',
                'vendor/assets/codemirror/codemirror.css'
            ],
            js: [
                'vendor/assets/codemirror/codemirror.js',
                'vendor/assets/marked/marked.js',
                'vendor/assets/uikit/js/components/htmleditor.min.js'
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

            this.$emit('ready');
        });

    },

    watch: {

        value: function (value) {
            if (this.editor && value != this.editor.editor.getValue()) {
                this.editor.editor.setValue(value);
            }
        },

        'options.markdown': function (markdown) {
            this.editor.trigger(markdown ? 'enableMarkdown' : 'disableMarkdown');
        }

    }

};
