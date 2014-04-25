define(['jquery', 'tmpl!link.modal,link.replace', 'uikit!htmleditor', 'link'], function($, tmpl, uikit, Link) {

    var modal  = $(tmpl.get('link.modal')).appendTo('body'),
        picker = new uikit.modal.Modal(modal),
        title  = modal.find('.js-title'),
        link, handler;

    modal.on('click', '.js-update', function() {
        handler();
    });

    uikit.htmleditor.addPlugin('link', function(editor) {

        var links = [];

        editor.element.on('render', function() {

            var regexp = editor.getMode() != 'gfm' ? /<a(?:.+?)>(?:[^<]*)<\/a>/gi : /<a(?:.+?)>(?:[^<]*)<\/a>|(^|[^!])(?:\[([^\n\]]*)\])(?:\(([^\n\]]*)\))?/gi;

            links = editor.replaceInPreview(regexp, function(data) {

                var pre = '';

                if (data.matches[0][0] == '<') {

                    var anchor = $(data.matches[0]);

                    data['link']    = anchor.attr('href');
                    data['txt']     = anchor.html();
                    data['class']   = anchor.attr('class') || '';
                    data['handler'] = function() {
                        picker.hide();

                        anchor.attr('href', link.get());
                        anchor.html(title.val());

                        data.replace(anchor[0].outerHTML);
                    };

                } else {

                    pre = data.matches[1];
                    data['link']    = data.matches[3].trim();
                    data['txt']     = data.matches[2].trim();
                    data['class']   = '';
                    data['handler'] = function() {
                        picker.hide();

                        data.replace(pre + '[' + title.val() + '](' + link.get() + ')');
                    };
                }

                return pre + tmpl.render('link.replace', { link: data['link'], txt: data['txt'], class: data['class']  }).replace(/(\r\n|\n|\r)/gm, '');

            });
        });

        editor.preview.on('click', '.js-editor-link', function(e) {
            e.preventDefault();

            var data = links[editor.preview.find('.js-editor-link').index(this)];

            handler = data.handler;

            title.val(data.txt);
            picker.show();
            setTimeout(function() { title.focus(); }, 10);

            link = Link.attach(modal.find('.js-linkpicker'), { value: data.link })
        });
    });
});
