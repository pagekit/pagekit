define(['jquery', 'tmpl!image.modal,image.replace', 'uikit', 'finder'], function($, tmpl, uikit, Finder) {

    var base    = requirejs.toUrl(''),
        modal   = $(tmpl.render('image.modal')).appendTo('body'),
        element = modal.find('.js-finder'),
        image   = modal.find('.js-url'),
        title   = modal.find('.js-title'),
        finder, handler, picker;

    modal.on('click', '.js-update', function() {
        handler();
    });

    element.on('picked', function(e, data) {

        if (data.type == 'file' && data.url.match(/\.(png|jpg|jpeg|gif|svg)$/i)) {

            var url = data.url;

            // convert to relative urls
            if (url.indexOf(base)===0) {
                url = url.replace(base,'');
            }

            image.val(url);
        }
    });

    return function(htmleditor, options) {

        var rootpath = options.root.replace(/^\/+|\/+$/g, "")+'/';

        htmleditor.addPlugin('htmlimages', /<img(.+?)>/gim, function(marker) {

            var attrs = {"src":"", "alt":""}, img;

            if (marker.found[0].match(/js\-no\-parse/)) {
                return marker.found[0];
            }

            img = $(marker.found[0]);

            attrs.src = img.attr("src") || "";
            attrs.alt = img.attr("alt") || "";

            if (!finder) {
                finder = new Finder(element, options);
                element.find('.js-finder-files').addClass('uk-overflow-container');
                picker  = new uikit.modal.Modal(modal)
            }

            marker.editor.preview.on('click', '#' + marker.uid + ' .js-config', function() {
                title.val(attrs.alt);
                image.val(attrs.src);

                //load finder in image dir
                if(attrs.src.trim() && attrs.src.indexOf(rootpath)===0) {
                    finder.loadPath( attrs.src.replace(rootpath, '').split('/').slice(0,-1).join('/') );
                }

                picker.show();
                setTimeout(function() { title.focus(); }, 10);

                handler = function() {
                    picker.hide();
                    img.attr("src", image.val());
                    img.attr("alt", title.val());
                    marker.replace(img[0].outerHTML);
                };
            });

            marker.editor.preview.on('click', '#' + marker.uid + ' .js-remove', function() {
                marker.replace('');
            });

            return tmpl.render('image.replace', { marker: marker, src: ((attrs.src.trim() && 'http://'!==attrs.src.trim()) ? attrs.src : false), alt: attrs.alt  }).replace(/(\r\n|\n|\r)/gm, '');
        });


        htmleditor.addPlugin('images', /(?:\{<(.*?)>\})?!(?:\[([^\n\]]*)\])(?:\(([^\n\]]*)\))?$/gim, function(marker) {

            if(marker.editor.editor.options.mode != "gfm") {
                return marker.found[0];
            }

            if (!finder) {
                finder = new Finder(element, options);
                element.find('.js-finder-files').addClass('uk-overflow-container');
                picker  = new uikit.modal.Modal(modal)
            }

            marker.editor.preview.on('click', '#' + marker.uid + ' .js-config', function() {
                title.val(marker.found[2]);
                image.val(marker.found[3]);

                //load finder in image dir
                if(marker.found[3].trim() && marker.found[3].indexOf(rootpath)===0) {
                    finder.loadPath( marker.found[3].replace(rootpath, '').split('/').slice(0,-1).join('/') );
                }

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

            return tmpl.render('image.replace', { marker: marker, src: ((marker.found[3] && 'http://'!==marker.found[3].trim()) ? marker.found[3] : false), alt: marker.found[2] }).replace(/(\r\n|\n|\r)/gm, '');
        });

        htmleditor.addPlugin('relativeimages', /src=[\"'](.+?)[\"']/gim, function(marker) {

            var replacement = marker.found[0];

            if(!marker.found[1].match(/^(\/|http\:|https\:|ftp\:)/i)) {
                replacement = replacement.replace(marker.found[1], base + marker.found[1]);
            }

            return replacement;
        });

    };
});
