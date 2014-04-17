define(['jquery', 'tmpl!image.modal,image.replace', 'uikit', 'finder'], function($, tmpl, uikit, Finder) {

    var base      = requirejs.toUrl(''),
        modal     = $(tmpl.render('image.modal')).appendTo('body'),
        element   = modal.find('.js-finder'),
        image     = modal.find('.js-url'),
        title     = modal.find('.js-title'),
        preview   = modal.find('.js-img-preview'),
        btnselect = modal.find('.js-select-image'),
        screens   = modal.find('[data-screen]').css({'animation-duration':'0.1s', '-webkit-animation-duration':'0.1s'}),
        goto      = function(screen) {

            var next = screens.filter('[data-screen="'+screen+'"]');

            screens.addClass('uk-hidden')
            next.removeClass('uk-hidden');

            picker.updateScrollable();
        },

        finder, handler, picker;

    modal.on('click', '.js-update', function() {
        handler();
    });

    modal.on('click', '[data-goto]', function(e){
        e.preventDefault();
        goto($(this).data('goto'));
    });

    element.on('selected-rows', function(e, rows) {

        if (rows.length === 1) {

            var data = $(rows[0]).data();

            if (data.type == 'file' && data.url.match(/\.(png|jpg|jpeg|gif|svg)$/i)) {
                btnselect.prop('disabled', false).data('url', data.url);
            }

        } else {
            btnselect.prop('disabled', true);
        }
    });

    btnselect.on('click', function() {

        var url = btnselect.data('url');

        updatePreview(url);

        // convert to relative urls
        if (url.indexOf(base) === 0) {
            url = url.replace(base, '');
        }

        image.val(url);

        goto('settings');
    });


    // preview
    var pimg;

    function updatePreview(url) {

        // convert to relative urls
        if (url && !url.match(/^(\/|http\:|https\:|ftp\:)/i)) {
            url = base + '/' + url;
        }

        var pimg = new Image();

        pimg.onerror = function(){
            preview.attr('src', base+'extensions/system/assets/images/placeholder-editor-image.svg');
        };

        pimg.onload = function(){
            preview.attr('src', url);
        };

        pimg.src = url;
    }

    function initFinder(options) {
        finder = new Finder(element, options);
        element.find('.js-finder-files').addClass('uk-overflow-container');
        picker = new uikit.modal.Modal(modal);

        element.find('.js-finder-toolbar-left').prepend(btnselect);
    }

    return function(htmleditor, options, editors) {

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

            if (!finder) {
                initFinder(options);
            }

            marker.editor.preview.on('click', '#' + marker.uid + ' .js-config', function() {
                title.val(attrs.alt);
                image.val(attrs.src);

                //load finder in image dir
                finder.loadPath(attrs.src.trim() && attrs.src.indexOf(rootpath) === 0 ? attrs.src.replace(rootpath, '').split('/').slice(0, -1).join('/') : '');

                updatePreview(image.val());
                goto('settings');
                picker.show();

                setTimeout(function() { title.focus(); }, 10);

                handler = function() {
                    picker.hide();
                    img.attr('src', image.val());
                    img.attr('alt', title.val());
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

            if (!finder) {
                initFinder(options);
            }

            marker.editor.preview.on('click', '#' + marker.uid + ' .js-config', function() {
                title.val(marker.found[2]);
                image.val(marker.found[3]);

                //load finder in image dir
                finder.loadPath(marker.found[3].trim() && marker.found[3].indexOf(rootpath) === 0 ? marker.found[3].replace(rootpath, '').split('/').slice(0, -1).join('/') : '');

                updatePreview(image.val());
                goto('settings');
                picker.show();
                setTimeout(function() { title.focus(); }, 10);

                handler = function() {
                    picker.hide();
                    marker.replace('![' + title.val() + '](' + image.val() + ')');
                };
            });

            marker.editor.preview.on('click', '#' + marker.uid + ' .js-remove', function() {
                marker.replace('');
            });

            return tmpl.render('image.replace', { marker: marker, src: ((marker.found[3] && 'http://' !== marker.found[3].trim()) ? marker.found[3] : false), alt: marker.found[2] }).replace(/(\r\n|\n|\r)/gm, '');
        });

        editors.forEach(function(editor) {
            editor.options.plugins.push('htmlimages');
            editor.options.plugins.push('images');
        });

    };
});
