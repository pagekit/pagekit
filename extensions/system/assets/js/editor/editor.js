define('editor', ['jquery', 'uikit!htmleditor', 'marked', 'codemirror'], function($, uikit, marked, codemirror) {

    return {

        attach: function(element, options) {
            return new uikit.htmleditor(element, $.extend({}, { marked: marked, CodeMirror: codemirror }, options));
        },

        addPlugin: function(name, plugin) {
            uikit.htmleditor.addPlugin(name, plugin);
        }

    };

});

require(['jquery', 'editor', 'domReady!'], function($, editor, doc) {

    $(doc).on('htmleditor-save', function(e, editor) {
        if (editor.element[0].form) {
            $(editor.element[0].form).submit();
        }
    });

    $('textarea[data-editor]').each(function() {
        editor.attach(this, $(this).data());
    });

    require($('script[data-editor]').data('editor'), function() {});

});