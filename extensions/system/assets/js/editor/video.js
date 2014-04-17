define(['jquery', 'tmpl!video.modal,video.replace', 'uikit', 'finder'], function($, tmpl, uikit, Finder) {

    var base      = requirejs.toUrl(''),
        modal     = $(tmpl.render('video.modal')).appendTo('body'),
        element   = modal.find('.js-finder'),
        video     = modal.find('.js-url'),
        preview   = modal.find('.js-video-preview'),
        btnselect = modal.find('.js-select-image'),
        screens   = modal.find('[data-screen]').css({'animation-duration':'0.1s', '-webkit-animation-duration':'0.1s'}),
        goto      = function(screen) {

            var next = screens.filter('[data-screen="'+screen+'"]');

            screens.addClass('uk-hidden')
            next.removeClass('uk-hidden');

            picker.updateScrollable();
        },
        handler, finder, picker;

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

            if (data.type == 'file' && data.url.match(/\.(mpeg|ogv|mp4|webm|wmv)$/i)) {
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

        video.val(url);

        goto('settings');
    });

    function updatePreview(url) {

        // convert to relative urls
        if (url && !url.match(/^(\/|http\:|https\:|ftp\:)/i)) {
            url = base + '/' + url;
        }

        preview.html(getVideoPreview(url));
    }

    function getVideoPreview(url) {

        var youtubeRegExp = /(\/\/.*?youtube\.[a-z]+)\/watch\?v=([^&]+)&?(.*)/,
            youtubeRegExpShort = /youtu\.be\/(.*)/,
            vimeoRegExp = /(\/\/.*?)vimeo\.[a-z]+\/([0-9]+).*?/,
            type = 'tag', code, matches, session = sessionStorage || {};

        if (matches = url.match(youtubeRegExp)) {

            code = '<img src="//img.youtube.com/vi/' + matches[2] + '/hqdefault.jpg" class="uk-width-1-1">';

        } else if (matches = url.match(youtubeRegExpShort)) {

            code = '<img src="//img.youtube.com/vi/' + matches[1] + '/hqdefault.jpg" class="uk-width-1-1">';

        } else if (url.match(vimeoRegExp)) {

            var imgid = btoa(url);

            if (session[imgid]) {
                code = '<img src="' + session[imgid] + '" class="uk-width-1-1">';
            } else {
                code = '<img data-imgid="' + imgid + '" src="" class="uk-width-1-1">';

                $.ajax({
                    type: 'GET',
                    url: 'http://vimeo.com/api/oembed.json?url=' + encodeURI(url),
                    jsonp: 'callback',
                    dataType: 'jsonp',
                    success: function(data) {
                        session[imgid] = data.thumbnail_url;
                        $('img[data-id="' + imgid + '"]').replaceWith('<img src="' + session[imgid] + '" class="uk-width-1-1">');
                    }
                });
            }
        }

        return code ? code : '<video class="uk-width-1-1" src="' + url + '"></video>';
    }

    return function(htmleditor, options, editors) {

        editors = editors || [];

        var rootpath = options.root.replace(/^\/+|\/+$/g, '')+'/';

        // videos
        htmleditor.commands.video = {
            title: 'Video',
            label: '<i class="uk-icon-video-camera"></i>',
            action: function(editor) {

                var replace = '(video){"src":"$1"}',
                    text = editor.getSelection(),
                    markdown = replace.replace('$1', text);

                editor.replaceSelection(markdown, 'end');
            }
        };

        htmleditor.defaults.toolbar.push('video');

        htmleditor.addPlugin('videos', /\(video\)\{(.+?)\}/gim, function(marker) {

            if (!finder) {

                finder = new Finder(element, options);
                element.find('.js-finder-files').addClass('uk-overflow-container');
                picker = new uikit.modal.Modal(modal);

                element.find('.js-finder-toolbar-left').prepend(btnselect);
            }

            var data = { src: '' };

            try {

                data = $.extend(data, (new Function('', 'var json = {' + marker.found[1] + '}; return JSON.parse(JSON.stringify(json));'))());

            } catch (e) {
            }

            marker.editor.preview.on('click', '#' + marker.uid + ' .js-config', function() {

                video.val(data.src);
                updatePreview(video.val());
                goto('settings');
                picker.show();
                setTimeout(function() {
                    video.focus();
                }, 10);

                handler = function() {
                    picker.hide();
                    marker.replace('(video)' + JSON.stringify({ src: video.val() }));
                };

                finder.loadPath(data.src.trim() && data.src.indexOf(rootpath) === 0 ? data.src.replace(rootpath, '').split('/').slice(0, -1).join('/') : '');
            });

            marker.editor.preview.on('click', '#' + marker.uid + ' .js-remove', function() {
                marker.replace('');
            });

            return tmpl.render('video.replace', { marker: marker, preview: getVideoPreview(data.src), src: data.src }).replace(/(\r\n|\n|\r)/gm, '');
        });

        editors.forEach(function(editor) {
            editor.options.toolbar.push('video');
            editor.options.plugins.push('videos');
        });
    };
});
