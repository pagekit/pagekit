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

        htmleditor.addPlugin('htmlimages', /<img(.+?)>/gim, function(marker) {

            var attrs = {"src":"", "alt":""};

            marker.found[0].match(/(\S+)=["']?((?:.(?!["']?\s+(?:\S+)=|[>"']))+.)["']?/g).forEach(function(attr){
                var parts = attr.replace(/('|")/g, '').split("=");
                attrs[parts[0]] = parts[1];
            });

            if (!finder) {
                finder = new Finder(element, options);
                element.find('.js-finder-files').addClass('uk-overflow-container');
                picker  = new uikit.modal.Modal(modal)
            }

            marker.editor.preview.on('click', '#' + marker.uid + ' .js-config', function() {
                title.val(attrs.alt);
                image.val(attrs.src);
                picker.show();
                setTimeout(function() { title.focus(); }, 10);

                handler = function() {
                    picker.hide();
                    marker.replace('<img src="' + image.val() + '" alt="' + title.val() + '">');
                };
            });

            marker.editor.preview.on('click', '#' + marker.uid + ' .js-remove', function() {
                marker.replace('');
            });

            return tmpl.render('image.replace', { marker: marker, src: ((attrs.src && 'http://'!==attrs.src.trim()) ? attrs.src : false), alt: attrs.alt  }).replace(/(\r\n|\n|\r)/gm, '');
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
