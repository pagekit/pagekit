define(['jquery', 'tmpl!image.modal,image.replace', 'uikit', 'finder'], function($, tmpl, uikit, Finder) {

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

    return function(htmleditor, options, editors) {

        ImagePopup.init(options);

        editors = editors || [];

        var rootpath = options.root.replace(/^\/+|\/+$/g, '')+'/';

        htmleditor.addPlugin('htmlimages', /<img(.+?)>/gim, function(marker) {

            var attrs = { src: '', alt: '' }, img;

            if (marker.found[0].match(/js\-no\-parse/)) {
                return marker.found[0];
            }

            img = $(marker.found[0]);

            attrs.src = img.attr('src') || '';
            attrs.alt = img.attr('alt') || '';

            marker.editor.preview.on('click', '#' + marker.uid + ' .js-config', function() {

                ImagePopup.title.val(attrs.alt);
                ImagePopup.image.val(attrs.src);

                ImagePopup.updatePreview(ImagePopup.image.val());
                ImagePopup.goto('settings');
                ImagePopup.getPicker().show();

                //load finder in image dir
                ImagePopup.finder.loadPath(attrs.src.trim() && attrs.src.indexOf(rootpath) === 0 ? attrs.src.replace(rootpath, '').split('/').slice(0, -1).join('/') : '');

                setTimeout(function() { ImagePopup.title.focus(); }, 10);

                ImagePopup.handler = function() {
                    ImagePopup.getPicker().hide();
                    img.attr('src', ImagePopup.image.val());
                    img.attr('alt', ImagePopup.title.val());
                    marker.replace(img[0].outerHTML);
                };
            });

            marker.editor.preview.on('click', '#' + marker.uid + ' .js-remove', function() {
                marker.replace('');
            });

            return tmpl.render('image.replace', { marker: marker, src: ((attrs.src.trim() && 'http://' !== attrs.src.trim()) ? attrs.src : false), alt: attrs.alt  }).replace(/(\r\n|\n|\r)/gm, '');
        });


        htmleditor.addPlugin('images', /(?:\{<(.*?)>\})?!(?:\[([^\n\]]*)\])(?:\(([^\n\]]*)\))?$/gim, function(marker) {

            if (marker.editor.editor.options.mode != 'gfm') {
                return marker.found[0];
            }

            marker.editor.preview.on('click', '#' + marker.uid + ' .js-config', function() {

                ImagePopup.title.val(marker.found[2]);
                ImagePopup.image.val(marker.found[3]);

                //load finder in image dir

                ImagePopup.updatePreview(ImagePopup.image.val());
                ImagePopup.goto('settings');
                ImagePopup.getPicker().show();

                ImagePopup.finder.loadPath(marker.found[3].trim() && marker.found[3].indexOf(rootpath) === 0 ? marker.found[3].replace(rootpath, '').split('/').slice(0, -1).join('/') : '');

                setTimeout(function() { ImagePopup.title.focus(); }, 10);

                ImagePopup.handler = function() {
                    ImagePopup.getPicker().hide();
                    marker.replace('![' + ImagePopup.title.val() + '](' + ImagePopup.image.val() + ')');
                };
            });

            marker.editor.preview.on('click', '#' + marker.uid + ' .js-remove', function() {
                marker.replace('');
            });

            return tmpl.render('image.replace', { marker: marker, src: ((marker.found[3] && 'http://' !== marker.found[3].trim()) ? marker.found[3] : false), alt: marker.found[2] }).replace(/(\r\n|\n|\r)/gm, '');
        });

        // override default image toolbar command
        htmleditor.commands.picture.action = function(editor) {

            var $this = this;

            ImagePopup.handler = function() {

                var repl;

                ImagePopup.getPicker().hide();

                if($this.getMode() == 'html') {
                    repl = '<img src="' + ImagePopup.image.val() + '" alt="' + ImagePopup.title.val() + '">';
                } else {
                    repl = '![' + ImagePopup.title.val() + '](' + ImagePopup.image.val() + ')';
                }

                editor.replaceSelection(repl, 'end');
            };

            ImagePopup.image.val('');
            ImagePopup.updatePreview(ImagePopup.image.val());
            ImagePopup.goto('settings');
            ImagePopup.getPicker().show();
            ImagePopup.finder.loadPath();

            setTimeout(function() {
                ImagePopup.image.focus();
            }, 10);
        };

        editors.forEach(function(editor) {
            editor.options.plugins.push('htmlimages');
            editor.options.plugins.push('images');
        });

    };
});
