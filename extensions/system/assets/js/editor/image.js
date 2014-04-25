define(['jquery', 'tmpl!image.modal,image.replace', 'uikit!htmleditor', 'finder'], function($, tmpl, uikit, Finder) {

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

            if(!this.picker) {

                this.finder = new Finder(this.element, this.options);
                this.element.find('.js-finder-files').addClass('uk-overflow-container');
                this.picker = new uikit.modal.Modal(this.modal);
                this.element.find('.js-finder-toolbar-left').prepend(this.btnselect);
            }

            return this.picker;
        },

        goto: function(screen){
            var next = this.screens.filter('[data-screen="'+screen+'"]');

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
                $this.preview.attr('src', $this.base+'extensions/system/assets/images/placeholder-editor-image.svg');
            };

            pimg.onload = function(){
                $this.preview.attr('src', url);
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

        setTimeout(function() { ImagePopup.title.focus(); }, 10);
    }

    uikit.htmleditor.addPlugin('image', function(editor) {

        var options = editor.element.data('finder'), rootpath = options.root.replace(/^\/+|\/+$/g, '')+'/', images = [];

        ImagePopup.init(options);

        editor.element.on('render', function() {

            var regexp = editor.getMode() != 'gfm' ? /<img(.+?)>/gi : /(?:<img(.+?)>|!(?:\[([^\n\]]*)\])(?:\(([^\n\]]*)\))?)/gi;

            images = editor.replaceInPreview(regexp, function(data) {

                if (data.matches[0][0] == '<') {

                    if (data.matches[0].match(/js\-no\-parse/)) {
                        return false;
                    }

                    var img = $(data.matches[0]);

                    data['src'] = img.attr('src') || '';
                    data['alt'] = img.attr('alt') || '';
                    data['handler'] = function() {
                        ImagePopup.getPicker().hide();

                        img.attr('src', ImagePopup.image.val());
                        img.attr('alt', ImagePopup.title.val());

                        data.replace(img[0].outerHTML);
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

        editor.element.off('action.picture');
        editor.element.on('action.picture', function() {

            var cursor = editor.editor.getCursor(), data;
            images.forEach(function(image) {
                if (image.inRange(cursor)) {
                    data = image;
                    return false;
                }
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
    });
});
