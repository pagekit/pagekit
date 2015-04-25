/**
 * Video plugin
 */

var $ = jQuery;

var VideoVm = {

    el: '#editor-video',

    data: {
        view: 'settings',
        video: {src: ''},
        finder: {root: '', select: ''}
    },

    ready: function () {

        var vm = this;

        this.$on('select.finder', function(selected) {
            if (selected.length == 1 && selected[0].match(/\.(mpeg|ogv|mp4|webm|wmv)$/i)) {
                vm.finder.select = selected[0];
            } else {
                vm.finder.select = '';
            }
        });

    },

    methods: {

        update: function () {

            var vid = this.video;

            vid.replace('(video)' + JSON.stringify({src: vid.src}));
        },

        preview: function (url) {

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
        },

        openFinder: function () {
            this.view = 'finder';
            this.finder.select = '';
        },

        closeFinder: function (select) {
            this.view = 'settings';
            if (select) this.video.src = select;
        }

    }

};

UIkit.plugin('htmleditor', 'video', {

    init: function(editor) {

        var self = this;

        this.editor = editor;
        this.videos = [];

        editor.addButton('video', {
            title: 'Video',
            label: '<i class="uk-icon-video-camera"></i>'
        });

        editor.element.on('action.video', function(e, editor) {

            var cursor = editor.getCursor(), video;

            self.videos.every(function(vid) {

                if (vid.inRange(cursor)) {
                    video = vid;
                    return false;
                }

                return true;
            });

            self.openModal(video);
        });

        editor.options.toolbar.push('video');

        editor.element.on('render', function() {
            self.videos = editor.replaceInPreview(/\(video\)(\{.+?\})/gi, self.replaceInPreview);
        });

        editor.preview.on('click', '.js-editor-video .js-config', function() {
            var index = editor.preview.find('.js-editor-video .js-config').index(this);
            self.openModal(self.videos[index]);
        });

        editor.preview.on('click', '.js-editor-video .js-remove', function() {
            var index = editor.preview.find('.js-editor-video .js-remove').index(this);
            self.videos[index].replace('');
        });

        return editor;
    },

    openModal: function(video) {

        var editor = this.editor, cursor = editor.editor.getCursor(), vm = $.extend(true, {}, VideoVm), modal;
        var options = editor.element.data('finder-options'), root = options.root.replace(/^\/+|\/+$/g, '')+'/';

        if (!video) {
            video = {
                src: '',
                replace: function (value) {
                    editor.editor.replaceRange(value, cursor);
                }
            };
        }

        modal = $(require('./modal.html')).appendTo('body');
        modal.on('hide.uk.modal', function() {
            $(this).remove();
        });

        UIkit.modal(modal).show();

        $.extend(vm.data.video, video);
        vm.data.finder.root = root;
        vm = new Vue(vm);
    },

    replaceInPreview: function(data) {

        var settings;

        try {

            settings = JSON.parse(data.matches[1]);

        } catch (e) {}

        $.extend(data, settings || { src: '' });

        return templates['video.replace'].template({src: data.src, preview: VideoVm.methods.preview(data.src)}).replace(/(\r\n|\n|\r)/gm, '');
    }

});
