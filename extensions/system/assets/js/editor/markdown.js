require(['jquery', 'uikit!htmleditor', 'marked', 'codemirror', 'domReady!'], function($, uikit, marked, codemirror) {

    var $script = $('script[data-plugins]'), plugins = $script.data('plugins'), options = $script.data('finder');

    require(plugins, function() {

        for (var plugin in arguments) {
            arguments[plugin](uikit.htmleditor, options);
        }

        $('[data-editor="markdown"]').each(function() {

            var editor = new uikit.htmleditor(this, { marked: marked, CodeMirror: codemirror, markdown: JSON.parse($(this).attr('markdown') || 'false') });

            editor.editor.addKeyMap({
                'Ctrl-S': function() { save(editor.element[0]); },
                'Cmd-S': function() { save(editor.element[0]); }
            });

            setTimeout(function() {
                editor.fit();
            }, 200);
        });
    });

    function save(textarea) {
        if (textarea.form) {
            $(textarea.form).submit();
        }
    }
});