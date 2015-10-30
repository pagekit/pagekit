module.exports = {

    ready: function () {

        var self = this, $el = $(this.$el), $parent = $el.parent();

        $parent.addClass('pk-editor');

        this.$asset({
            css: [
                'app/assets/codemirror/hint.css',
                'app/assets/codemirror/codemirror.css'
            ],
            js: [
                'app/assets/codemirror/codemirror.js'
            ]

        }, function () {

            this.editor = CodeMirror.fromTextArea(this.$el, _.extend({
                mode: 'htmlmixed',
                dragDrop: false,
                autoCloseTags: true,
                matchTags: true,
                autoCloseBrackets: true,
                matchBrackets: true,
                indentUnit: 4,
                indentWithTabs: false,
                tabSize: 4
            }, this.options));

            $parent.attr('data-uk-check-display', 'true').on('display.uk.check', function (e) {
                self.editor.refresh();
            });

            this.editor.on('change', function () {
                self.editor.save();
                $el.trigger('input');
            });

            this.$watch('value', function (value) {
                if (value != this.editor.getValue()) {
                    this.editor.setValue(value);
                }
            });

            this.$emit('ready');

        });
    }

};
