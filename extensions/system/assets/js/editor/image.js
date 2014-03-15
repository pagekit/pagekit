define(['jquery', 'tmpl!image.modal,image.replace', 'uikit', 'finder'], function($, tmpl, uikit, Finder) {

    var modal   = $(tmpl.render('image.modal')).appendTo('body'),
        element = modal.find('.js-finder'),
        image   = modal.find('.js-url'),
        title   = modal.find('.js-title'),
        finder, handler, picker;

    modal.on('click', '.js-update', function() {
        handler();
    });

    element.on('picked', function(e, data) {
        if (data.type == 'file' && data.url.match(/\.(png|jpg|jpeg|gif|svg)$/i)) {
            image.val(data.url);
        }
    });

    return function(markdownarea, options) {

        markdownarea.addPlugin('images', /(?:\{<(.*?)>\})?!(?:\[([^\n\]]*)\])(?:\(([^\n\]]*)\))?$/gim, function(marker) {

            if (!finder) {
                finder = new Finder(element, options);
                element.find('.js-finder-files').addClass('uk-overflow-container');
                picker  = new uikit.modal.Modal(modal)
            }

            marker.area.preview.on('click', '#' + marker.uid + ' .js-config', function() {
                title.val(marker.found[2]);
                image.val(marker.found[3]);
                picker.show();
                setTimeout(function() { title.focus(); }, 10);

                handler = function() {
                    picker.hide();
                    marker.replace('![' + title.val() + '](' + image.val() + ')');
                };
            });

            marker.area.preview.on('click', '#' + marker.uid + ' .js-remove', function() {
                marker.replace('');
            });

            return tmpl.render('image.replace', { marker: marker, src: (marker.found[3] && 'http://'!==marker.found[3].trim())  }).replace(/(\r\n|\n|\r)/gm, '');
        });

    };
});
