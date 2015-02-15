define('editor', ['jquery', 'uikit!htmleditor', 'marked', 'codemirror'], function($, uikit, marked, codemirror) {

    return {

        attach: function(element, options) {
            options.markdown = ('markdown' in options) && (options.markdown === "" || options.markdown);

            return uikit.htmleditor(element, $.extend({}, { marked: marked, CodeMirror: codemirror }, options));
        }
    };
});

require(['jquery', 'editor', 'uikit', 'domReady!'], function($, editor, uikit, doc) {

    $(doc).on('htmleditor-save', function(e, editor) {
        if (editor.element[0].form) {
            $(editor.element[0].form).submit();
        }
    });

    var editors = $('textarea[data-editor]').each(function() {
        editor.attach(this, $(this).data());
    });

    require($('script[data-editor]').data('editor'), function() {

        editors.each(function(){

            var editor = $(this).data('htmleditor');

            $.each(uikit.components.htmleditor.plugins, function(name, plugin){
                if ((!editor.options.plugins.length || editor.options.plugins.indexOf(name) >= 0) && !editor.plugins[name]) {
                    plugin.init(editor);
                    editor.plugins[name] = true;
                }
            });

            editor.debouncedRedraw();
        });
    });

});