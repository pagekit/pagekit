define(['jquery', 'uikit!htmleditor'], function($, uikit) {

    var base = requirejs.toUrl('');

    uikit.htmleditor.addPlugin('urlresolver', function(editor) {

        editor.element.on('renderLate', function() {

            editor.replaceInPreview(/src=["'](.+?)["']/gi, function(data) {

                var replacement = data.matches[0];

                if (!data.matches[1].match(/^(\/|http:|https:|ftp:)/i)) {
                    replacement = replacement.replace(data.matches[1], base + data.matches[1]);
                }

                return replacement;
            });

        });

    });
});
