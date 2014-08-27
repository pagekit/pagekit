define(['jquery', 'tmpl!link.modal,link.replace', 'uikit', 'editor', 'system!link'], function($, tmpl, uikit, editor, system) {

    var modal           = $(tmpl.get('link.modal')).appendTo('body'),
        picker          = uikit.modal(modal),
        title           = modal.find('.js-title'),
        target          = modal.find('.js-target'),
        targetOther     = modal.find('.js-target_other'),
        follow          = modal.find('.js-follow'),
        link, handler;

    modal.on('click', '.js-update', function() {
        handler();
    });

    target.on('change', function(){
        if($(this).val() === 'other') {
            targetOther.parents('.uk-form-row').slideDown();
        }
        else{
            targetOther.parents('.uk-form-row').slideUp();
        }
    });

    function openLinkModal(data) {
        handler = data.handler;
     
        title.val(data.txt);
        target.val(data.target).change();
        targetOther.val(data.targetOther);
        follow.attr('checked', (data.follow == 'nofollow'));
        picker.show();
        setTimeout(function() { title.focus(); }, 10);
        
        link = system.link(modal.find('.js-linkpicker'), { value: data.link });
    }

    uikit.plugin('htmleditor', 'link', {

        init: function(editor) {

            var links = [];

            editor.element.on('render', function() {

                var regexp = editor.getMode() != 'gfm' ? /<a(?:.+?)>(?:[^<]*)<\/a>/gi : /<a(?:.+?)>(?:[^<]*)<\/a>|(?:\[([^\n\]]*)\])(?:\(([^\n\]]*)\))(?:\{([^\n\]]*)\})?/gi;

                links = editor.replaceInPreview(regexp, function(data) {

                    if (data.matches[0][0] == '<') {

                        var anchor  = $(data.matches[0]);
                        var other   = ($.inArray(anchor.attr('target'), ['', '_blank', '_top', '_parent']) != -1 
                                    || typeof anchor.attr('target') === 'undefined' );

                        data['link']            =   anchor.attr('href');
                        data['txt']             =   anchor.html();
                        data['target']          =   (other) ? anchor.attr('target') || '' : 'other';
                        data['targetOther']     =   (!other) ? anchor.attr('target') || '' : '';
                        data['follow']          =   anchor.attr('rel') || '';
                        data['class']           =   anchor.attr('class') || '';
                        data['handler']         =   function() {
                            picker.hide();
                            var targetSelected = target.val();
                            if(targetSelected !== '' ) {
                                if(targetSelected !== 'other')
                                    anchor.attr('target', targetSelected);
                                else if(targetOther.val() !== '') 
                                    anchor.attr('target', targetOther.val());
                                else
                                    anchor.removeAttr('target');
                            }
                            else 
                                anchor.removeAttr('target');

                            if(follow.is(':checked'))
                                anchor.attr('rel', 'nofollow');
                            else
                                anchor.removeAttr('rel');

                            anchor.attr('href', link.get());
                            anchor.html(title.val());

                            data.replace(anchor[0].outerHTML);
                        };

                    } else {

                        if (data.matches[data.matches.length - 1][data.matches[data.matches.length - 2] - 1] == '!') return false;

                        var defaultAdvanced = '"target": "", "rel": ""'; 
                        var advanced = (typeof data.matches[3] !== 'undefined') ? data.matches[3] : defaultAdvanced;
                        advanced = $.parseJSON('{' + advanced + '}');

                        var other   = (advanced.target && $.inArray(advanced.target, ['', '_blank', '_top', '_parent']) != -1 
                                    || typeof advanced.target === 'undefined' );


                        data['link']            = data.matches[2];
                        data['txt']             = data.matches[1];
                        data['target']          = (other) ? advanced.target || '' : 'other';
                        data['targetOther']     = (!other) ? advanced.target || '' : '';
                        data['follow']          = advanced.rel || '';
                        data['class']           = '';
                        data['handler']         = function() {
                            picker.hide();
                            var advancedValues = {};
                            var targetSelected = target.val();
                            if(targetSelected !== '' ) {
                                if(targetSelected !== 'other')
                                    advancedValues.target = targetSelected;
                                else if(targetOther.val() !== '') 
                                    advancedValues.target = targetOther.val();
                            }

                            if(follow.is(':checked'))
                                advancedValues.rel = 'nofollow';    

                            advancedValues = (!$.isEmptyObject(advancedValues)) ? JSON.stringify(advancedValues) : '';

                            data.replace('[' + title.val() + '](' + link.get() + ')' + advancedValues);
                        };
                    }
                    return tmpl.render('link.replace', { link: data['link'], txt: data['txt'], class: data['class']  }).replace(/(\r\n|\n|\r)/gm, '');

                });
            });

            editor.preview.on('click', '.js-editor-link', function(e) {
                e.preventDefault();
                openLinkModal(links[editor.preview.find('.js-editor-link').index(this)]);
            });

            editor.element.off('action.link');
            editor.element.on('action.link', function() {

                var cursor = editor.editor.getCursor(), data;

                links.every(function(link) {
                    if (link.inRange(cursor)) {
                        data = link;
                        return false;
                    }
                    return true;
                });

                if (!data) {

                    data = {
                        txt: editor.editor.getSelection(),
                        link: 'http://',
                        target: '',
                        targetOther: '',
                        follow: '',
                        'class': '',

                        handler: function() {

                            var repl;

                            picker.hide();
                            if (editor.getCursorMode() == 'html') {
                                var targetSelected = (target.val() != '') ? 'target="'+ target.val() +'"' : '';
                                repl = '<a href="' + link.get() + '" ' + targetSelected + ' >' + title.val() + '</a>';
                            } else {
                                var advancedValues = {};
                                var targetSelected = target.val();
                                if(targetSelected !== '' ) {
                                    if(targetSelected !== 'other')
                                        advancedValues.target = targetSelected;
                                    else if(targetOther.val() !== '') 
                                        advancedValues.target = targetOther.val();
                                }

                                if(follow.is(':checked'))
                                    advancedValues.rel = 'nofollow';    

                                advancedValues = (!$.isEmptyObject(advancedValues)) ? JSON.stringify(advancedValues) : '';

                                // {:target="_blank"} // syntaxe to use for extra data
                                repl = '[' + title.val() + '](' + link.get() + ')' + advancedValues;
                            }

                            editor.editor.replaceSelection(repl, 'end');
                        },
                        replace: function(value) { editor.editor.replaceRange(value, cursor); }
                    };
                }

                openLinkModal(data);
            });

            return editor;
        }
    });
});
