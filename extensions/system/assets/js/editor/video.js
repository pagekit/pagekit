define(['jquery', 'system!finder', 'tmpl!video.modal,video.replace', 'uikit', 'editor'], function($, system, tmpl, uikit, editor) {

    var VideoPopup = {

        init: function(options){

            var $this = this;

            this.options   = options;

            this.base      = requirejs.toUrl('');
            this.modal     = $(tmpl.render('video.modal')).appendTo('body');
            this.element   = this.modal.find('.js-finder');
            this.video     = this.modal.find('.js-url');
            this.preview   = this.modal.find('.js-video-preview');
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

                    if (data.type == 'file' && data.url.match(/\.(mpeg|ogv|mp4|webm|wmv)$/i)) {
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

                $this.video.val(url);
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

            this.screens.addClass('uk-hidden');
            next.removeClass('uk-hidden');

            this.getPicker().updateScrollable();
        },

        updatePreview: function(url) {
            // convert to relative urls
            if (url && !url.match(/^(\/|http\:|https\:|ftp\:)/i)) {
                url = this.base + '/' + url;
            }

            this.preview.html(getVideoPreview(url));
        }
    };


    function getVideoPreview(url) {

        var youtubeRegExp = /(\/\/.*?youtube\.[a-z]+)\/watch\?v=([^&]+)&?(.*)/,
            youtubeRegExpShort = /youtu\.be\/(.*)/,
            vimeoRegExp = /(\/\/.*?)vimeo\.[a-z]+\/([0-9]+).*?/,
            code, matches, session = sessionStorage || {};

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

    function openVideoModal(data, rootpath) {

        VideoPopup.video.val(data.src);
        VideoPopup.updatePreview(VideoPopup.video.val());
        VideoPopup.goto('settings');
        VideoPopup.getPicker().show();

        setTimeout(function() {
            VideoPopup.video.focus();
        }, 10);

        VideoPopup.handler = function() {
            VideoPopup.getPicker().hide();

            data.replace('(video)' + JSON.stringify({ src: VideoPopup.video.val() }));
        };

        VideoPopup.finder.loadPath(data.src.trim() && data.src.indexOf(rootpath) === 0 ? data.src.replace(rootpath, '').split('/').slice(0, -1).join('/') : '');
    }

    uikit.plugin('htmleditor', 'video', {

        init: function(editor) {

            var options = editor.element.data('finder'), rootpath = options.root.replace(/^\/+|\/+$/g, '')+'/', videos = [];

            VideoPopup.init(options);

            // videos
            editor.addButton('video', {
                title: 'Video',
                label: '<i class="uk-icon-video-camera"></i>'
            });

            editor.element.on('action.video', function(e, editor) {

                var cursor = editor.getCursor(), data;
                videos.every(function(video) {
                    if (video.inRange(cursor)) {
                        data = video;
                        return false;
                    }
                    return true;
                });

                if (!data) {
                    data = { src: '', replace: function(value) { editor.replaceRange(value, cursor); } };
                }

                openVideoModal(data, rootpath);
            });

            editor.options.toolbar.push('video');

            editor.element.on('render', function() {

                videos = editor.replaceInPreview(/\(video\)(\{.+?\})/gi, function(data) {

                    try {

                        var settings = $.parseJSON(data.matches[1]);

                    } catch (e) {}

                    $.extend(data, (settings || { src: '' }));

                    return tmpl.render('video.replace', { preview: getVideoPreview(data.src), src: data.src }).replace(/(\r\n|\n|\r)/gm, '');

                });
            });

            editor.preview.on('click', '.js-editor-video .js-config', function() {
                openVideoModal(videos[editor.preview.find('.js-editor-video .js-config').index(this)], rootpath);
            });

            editor.preview.on('click', '.js-editor-video .js-remove', function() {
                videos[editor.preview.find('.js-editor-video .js-remove').index(this)].replace('');
            });

            return editor;
        }
    });
});
