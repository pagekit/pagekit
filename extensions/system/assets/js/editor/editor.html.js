define('editor.html', ['jquery', 'uikit!htmleditor', 'marked', 'codemirror'], function($, uikit, marked, codemirror) {

    $(document).on('htmleditor-save', function(e, editor) {
        if (editor.element[0].form) {
            $(editor.element[0].form).submit();
        }
    });

    return {

        attach: function(element, options) {

            return new uikit.htmleditor(element, $.extend({}, { marked: marked, CodeMirror: codemirror }, options));

        },

        addPlugin: function(name, plugin) {
            uikit.htmleditor.addPlugin(name, plugin);
        }

    }

});

require(['jquery', 'editor.html', 'domReady!'], function($, editor) {

    $('[data-editor="markdown"]').each(function() {
        editor.attach(this, $(this).data());
    });

    require($('script[data-plugins]').data('plugins'), function() {});

});