(function($) {

    $(function() {

        $(document).on('htmleditor-save', function(e, editor) {
            if (editor.element[0].form) {
                $(editor.element[0].form).submit();
            }
        });

        $('textarea[data-editor]').each(function() {

            var options = $(this).data();

            options.markdown = ('markdown' in options) && (options.markdown === '' || options.markdown);
            UIkit.htmleditor(this, $.extend({}, { marked: marked, CodeMirror: CodeMirror }, options));
        });

    });

    UIkit.plugin('htmleditor', 'urlresolver', {

        init: function(editor) {

            editor.element.on('renderLate', function() {

                editor.replaceInPreview(/src=["'](.+?)["']/gi, function(data) {

                    var replacement = data.matches[0];

                    if (!data.matches[1].match(/^(\/|http:|https:|ftp:)/i)) {
                        replacement = replacement.replace(data.matches[1], System.url(data.matches[1], true));
                    }

                    return replacement;
                });

            });

            return editor;
        }

    });

    UIkit.plugin('htmleditor', 'image', {

        init: function(editor) {

            System.template('image.modal');

            return editor;
        }

    });

})(jQuery);