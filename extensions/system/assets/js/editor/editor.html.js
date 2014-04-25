define('editor.html', ['jquery', 'uikit!htmleditor', 'marked', 'codemirror'], function($, uikit, marked, codemirror) {

    $(document).on('htmleditor-save', function(e, editor) {
        if (editor.element[0].form) {
            $(editor.element[0].form).submit();
        }
    });

    return {

        attach: function(element, options) {

            var editor = new uikit.htmleditor(element, { marked: marked, CodeMirror: codemirror, markdown: options.markdown, plugins: options.plugins });

            $(this).on('editor.plugins.loaded', function() {
                editor.initPlugins();
            });

            return editor;
        }

    }

});

require(['jquery', 'editor.html', 'domReady!'], function($, editor) {

    $('[data-editor="markdown"]').each(function() {
        editor.attach(this, $(this).data());
    });

    require($('script[data-plugins]').data('plugins'), function() {
        $(editor).trigger('editor.plugins.loaded');
    });

});