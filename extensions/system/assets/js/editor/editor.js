define('editor', ['jquery', 'uikit!htmleditor', 'marked', 'codemirror'], function($, uikit, marked, codemirror) {

    return {

        attach: function(element, options) {
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

        var plugins = Object.keys(uikit.components.htmleditor.plugins).filter(function(plugin) {
            return (plugin != 'base' && plugin != 'markdown');
        });

        editors.each(function(){

            var editor = $(this).data('htmleditor');

            plugins.forEach(function(plugin){
                uikit.components.htmleditor.plugins[plugin].init(editor);
                editor.options.plugins.push(plugin);
            });

            editor.debouncedRedraw();
        });
    });

});