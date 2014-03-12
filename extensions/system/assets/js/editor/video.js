define(['jquery', 'tmpl!video.modal,video.replace', 'uikit', 'finder'], function($, tmpl, uikit, Finder) {

    var modal   = $(tmpl.render('video.modal')).appendTo('body'),
        element = modal.find('.js-finder'),
        video   = modal.find('.js-url'),
        handler, finder, picker;

    modal.on('click', '.js-update', function() {
        handler();
    });

    element.on('picked', function(e, data) {
        // TODO: add video formats
        if (data.type == 'file' && data.url.match(/\.(mpeg|ogv|mp4|webm|wmv)$/i)) {
            video.val(data.url);
        }
    });

    return function(markdownarea, options) {

        // videos
        markdownarea.commands.video = {
            title: 'Video',
            label: '<i class="uk-icon-video-camera"></i>',
            action: function(editor) {

                var replace = '(video){"src":"$1"}',
                    text = editor.getSelection(),
                    markdown = replace.replace('$1', text);

                editor.replaceSelection(markdown, 'end');
            }
        };

        markdownarea.defaults.toolbar.push('video');

        function getVideoPreview(url) {

            var youtubeRegExp = /(\/\/.*?youtube\.[a-z]+)\/watch\?v=([^&]+)&?(.*)/,
                youtubeRegExpShort = /youtu\.be\/(.*)/,
                vimeoRegExp = /(\/\/.*?)vimeo\.[a-z]+\/([0-9]+).*?/,
                type = 'tag', code, matches, session = sessionStorage || {};

            if (matches = url.match(youtubeRegExp)) {

                code = '<img src="//img.youtube.com/vi/' + matches[2] + '/hqdefault.jpg" class="uk-width-1-1">';

            } else if (matches = url.match(youtubeRegExpShort)) {

                code = '<img src="//img.youtube.com/vi/' + matches[1] + '/hqdefault.jpg" class="uk-width-1-1">';

            } else if (matches = url.match(vimeoRegExp)) {

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

        markdownarea.addPlugin('videos', /\(video\)\{(.+?)\}/gim, function(marker) {

            if (!finder) {

                finder = new Finder(element, options);
                element.find('.js-finder-files').addClass('uk-modal-scrollable-box');
                picker = new uikit.modal.Modal(modal)
            }

            var data = { src: '' };

            try {

                data = $.extend(data, (new Function('', 'var json = {' + marker.found[1] + '}; return JSON.parse(JSON.stringify(json));'))());

            } catch (e) {
            }

            marker.area.preview.on('click', '#' + marker.uid + ' .js-config', function() {

                video.val(data.src);
                picker.show();
                setTimeout(function() {
                    video.focus();
                }, 10);

                handler = function() {
                    picker.hide();
                    marker.replace('(video)' + JSON.stringify({ src: video.val() }));
                };
            });

            marker.area.preview.on('click', '#' + marker.uid + ' .js-remove', function() {
                marker.replace('');
            });

            return tmpl.render('video.replace', { marker: marker, preview: getVideoPreview(data.src), src: data.src }).replace(/(\r\n|\n|\r)/gm, '');
        });
    };
});
