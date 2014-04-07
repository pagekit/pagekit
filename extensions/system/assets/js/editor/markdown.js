require(['jquery', 'uikit!htmleditor', 'marked', 'codemirror', 'domReady!'], function($, uikit, marked, codemirror) {

    var $script = $('script[data-plugins]');
    plugins = $script.data('plugins'), options = $script.data('finder');

    require(plugins, function() {

        for (var plugin in arguments) {
            arguments[plugin](uikit.htmleditor, options);
        }

        uikit.htmleditor.defaults.codemirror.autoCloseTags = true;
        uikit.htmleditor.defaults.codemirror.matchTags     = true;

        $('[data-editor="markdown"]').each(function() {
            var editor = new uikit.htmleditor(this, { marked: marked, CodeMirror: codemirror, markdown:true });

            editor.editor.on('inputRead', uikit.Utils.debounce(function() {
                autocomplete(editor.editor);
            }, 100));

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
            textarea.form.submit();
        }
    }


    function autocomplete(cm) {
        var doc = cm.getDoc(), POS = doc.getCursor(), mode = CodeMirror.innerMode(cm.getMode(), cm.getTokenAt(POS).state).mode.name;

        if (mode == 'xml') { //html depends on xml

            var cur = cm.getCursor(), token = cm.getTokenAt(cur);

            if (token.string.charAt(0) == "<" || token.type == "attribute") {
                CodeMirror.showHint(cm, CodeMirror.hint.html, { completeSingle: false });
            }
        }
    };
});