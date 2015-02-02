define(['jquery', 'system!finder', 'tmpl!image.modal,image.replace', 'uikit', 'editor'], function($, system, tmpl, uikit, editor) {

    var ImagePopup = {

        init: function(options){

            var $this = this;

            this.options   = options;

            this.base      = requirejs.toUrl('');
            this.modal     = $(tmpl.render('image.modal')).appendTo('body');
            this.element   = this.modal.find('.js-finder');
            this.image     = this.modal.find('.js-url');
            this.title     = this.modal.find('.js-title');
            this.preview   = this.modal.find('.js-img-preview');
            this.btnselect = this.modal.find('.js-select-image');
            this.screens   = this.modal.find('[data-screen]').css({'animation-duration':'0.1s', '-webkit-animation-duration':'0.1s'});
            this.finder    = null;
            this.picker    = null;
            this.handler   = null;

            // events
            this.modal.on('click', '.js-update', function() {
                $this.handler();
            });

            this.modal.on('click', '[data-goto]', function(e){
                e.preventDefault();
                $this.goto($(this).data('goto'));
            });

            this.element.on('selected-rows', function(e, rows) {

                if (rows.length === 1) {

                    var data = $(rows[0]).data();

                    if (data.type == 'file' && data.url.match(/\.(png|jpg|jpeg|gif|svg)$/i)) {
                        $this.btnselect.prop('disabled', false).data('url', data.url);
                    }

                } else {
                    $this.btnselect.prop('disabled', true);
                }
            });

            this.btnselect.on('click', function() {

                var url = $this.btnselect.data('url');

                $this.updatePreview(url);

                // convert to relative urls
                if (url.indexOf($this.base) === 0) {
                    url = url.replace($this.base, '');
                }

                $this.image.val(url);
                $this.goto('settings');
            });
        },

        getPicker: function() {

            if (!this.picker) {
                this.finder = system.finder(this.element, this.options);
                this.element.find('.js-finder-files').addClass('uk-overflow-container');
                this.picker = uikit.modal(this.modal);
            }

            return this.picker;
        },

        goto: function(screen){
            var next = this.screens.filter('[data-screen="'+screen+'"]');

            if (screen=='settings') {
                this.modal.find('.uk-modal-dialog').removeClass('uk-modal-dialog-large');
            } else {
                this.modal.find('.uk-modal-dialog').addClass('uk-modal-dialog-large');
            }

            this.screens.addClass('uk-hidden');
            next.removeClass('uk-hidden');

            this.getPicker().updateScrollable();
        },

        updatePreview: function(url) {

            var $this = this;

            // convert to relative urls
            if (url && !url.match(/^(\/|http\:|https\:|ftp\:)/i)) {
                url = this.base + '/' + url;
            }

            var pimg = new Image();

            pimg.onerror = function(){
                $this.preview.css('background-image', '').css('background-size', '');
            };

            pimg.onload = function(){
                $this.preview.css('background-image', 'url("'+url+'")').css('background-size', 'contain');
            };

            pimg.src = url;
        }

    };

    function openImageModal(data, rootpath) {

        ImagePopup.handler = data.handler;

        ImagePopup.title.val(data.alt);
        ImagePopup.image.val(data.src);

        //load finder in image dir

        ImagePopup.updatePreview(ImagePopup.image.val());
        ImagePopup.goto('settings');
        ImagePopup.getPicker().show();

        ImagePopup.finder.loadPath(data.src.trim && data.src.indexOf(rootpath) === 0 ? data.src.replace(rootpath, '').split('/').slice(0, -1).join('/') : '');

        setTimeout(function() { ImagePopup.image.focus(); }, 10);
    }

    uikit.plugin('htmleditor', 'image', {

        init: function(editor) {

            var options = editor.element.data('finder'), rootpath = options.root.replace(/^\/+|\/+$/g, '')+'/', images = [];

            ImagePopup.init(options);

            editor.element.on('render', function() {

                var regexp = editor.getMode() != 'gfm' ? /<img(.+?)>/gi : /(?:<img(.+?)>|!(?:\[([^\n\]]*)\])(?:\(([^\n\]]*)\))?)/gi;

                images = editor.replaceInPreview(regexp, function(data) {

                    if (data.matches[0][0] == '<') {

                        if (data.matches[0].match(/js\-no\-parse/)) return false;

                        var matchesSrc = data.matches[0].match(/\ssrc="(.*?)"/),
                            matchesAlt = data.matches[0].match(/\salt="(.*?)"/);

                        data['src'] = matchesSrc ? matchesSrc[1] : '';
                        data['alt'] = matchesAlt ? matchesAlt[1] : '';
                        data['handler'] = function() {
                            ImagePopup.getPicker().hide();

                            var src = ' src="' + ImagePopup.image.val()+'"', alt = ' alt="'+ImagePopup.title.val() + '"', output = data.matches[0];

                            output = matchesSrc ? output.replace(matchesSrc[0], src) : [output.slice(0, 4), src, output.slice(4)].join('');
                            output = matchesAlt ? output.replace(matchesAlt[0], alt) : [output.slice(0, 4), alt, output.slice(4)].join('');

                            data.replace(output);
                        };

                    } else {

                        data['src'] = data.matches[3].trim();
                        data['alt'] = data.matches[2];
                        data['handler'] = function() {
                            ImagePopup.getPicker().hide();
                            data.replace('![' + ImagePopup.title.val() + '](' + ImagePopup.image.val() + ')');
                        };

                    }

                    return tmpl.render('image.replace', { src: ('http://' !== data['src'] ? data['src'] : ''), alt: data['alt']  }).replace(/(\r\n|\n|\r)/gm, '');

                });
            });

            editor.preview.on('click', '.js-editor-image .js-config', function() {
                openImageModal(images[editor.preview.find('.js-editor-image .js-config').index(this)], rootpath);
            });

            editor.preview.on('click', '.js-editor-image .js-remove', function() {
                images[editor.preview.find('.js-editor-image .js-remove').index(this)].replace('');
            });

            editor.element.off('action.image');
            editor.element.on('action.image', function() {

                var cursor = editor.editor.getCursor(), data;
                images.every(function(image) {
                    if (image.inRange(cursor)) {
                        data = image;
                        return false;
                    }
                    return true;
                });

                if (!data) {
                    data = {
                        src: '',
                        alt: '',
                        handler: function() {

                            var repl;

                            ImagePopup.getPicker().hide();

                            if (editor.getCursorMode() == 'html') {
                                repl = '<img src="' + ImagePopup.image.val() + '" alt="' + ImagePopup.title.val() + '">';
                            } else {
                                repl = '![' + ImagePopup.title.val() + '](' + ImagePopup.image.val() + ')';
                            }

                            editor.editor.replaceSelection(repl, 'end');
                        },
                        replace: function(value) { editor.editor.replaceRange(value, cursor); }
                    };
                }

                openImageModal(data, rootpath);
            });

            return editor;

        }
    });
});
