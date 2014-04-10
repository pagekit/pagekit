require(['jquery', 'uikit!htmleditor', 'marked', 'codemirror', 'domReady!'], function($, uikit, marked, codemirror) {

    var $script = $('script[data-plugins]'), plugins = $script.data('plugins'), options = $script.data('finder'), editors = [];

    $('[data-editor="markdown"]').each(function() {

        var editor = new uikit.htmleditor(this, { marked: marked, CodeMirror: codemirror, markdown: JSON.parse($(this).attr('markdown') || 'false') });

        editor.editor.addKeyMap({
            'Ctrl-S': function() { save(editor.element[0]); },
            'Cmd-S': function() { save(editor.element[0]); }
        });

        editors.push(editor);

        setTimeout(function() {
            editor.fit();
        }, 200);
    });

    // load plugins
    require(plugins, function() {

        for (var plugin in arguments) {
            arguments[plugin](uikit.htmleditor, options, editors);
        }

        // refresh editors
        editors.forEach(function(editor) {
            editor.redraw();
        });
    });

    function save(textarea) {
        if (textarea.form) {
            $(textarea.form).submit();
        }
    }
});