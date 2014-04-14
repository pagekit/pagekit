require(['jquery', 'uikit!htmleditor', 'marked', 'codemirror', 'domReady!'], function($, uikit, marked, codemirror) {

    var $script = $('script[data-plugins]'), plugins = $script.data('plugins'), options = $script.data('finder'), editors = [], base = requirejs.toUrl('');

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

    // relative image/video src resolver plugin
    uikit.htmleditor.addPlugin('relativesource', /src=["'](.+?)["']/gim, function(marker) {

        var replacement = marker.found[0];

        if(!marker.found[1].match(/^(\/|http\:|https\:|ftp\:)/i)) {
            replacement = replacement.replace(marker.found[1], base + marker.found[1]);
        }

        return replacement;
    });

    // load plugins
    require(plugins, function() {

        for (var plugin in arguments) {
            arguments[plugin](uikit.htmleditor, options, editors);
        }

        // refresh editors
        editors.forEach(function(editor) {
            editor.options.plugins.push('relativesource');
            editor.redraw();
        });
    });

    function save(textarea) {
        if (textarea.form) {
            $(textarea.form).submit();
        }
    }
});