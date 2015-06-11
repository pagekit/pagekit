/**
 * Editor Video plugin.
 */

var $ = require('jquery');
var _ = require('lodash');
var Picker = require('./picker.vue');

require('../util.js');

module.exports = {

    plugin: {
        name: 'video'
    },

    methods: {

        init: function () {

            var vm = this, editor = this.editor;

            if (!editor || !editor.htmleditor) {
                return;
            }

            this.videos = [];

            editor.addButton('video', {
                title: 'Video',
                label: '<i class="uk-icon-video-camera"></i>'
            });

            editor.options.toolbar.push('video');

            editor.element
                .on('action.video', function(e, editor) {
                    vm.openModal(_.find(vm.videos, function(vid) {
                        return vid.inRange(editor.getCursor());
                    }));
                })
                .on('render', function() {
                    vm.videos = editor.replaceInPreview(/\(video\)(\{.+?})/gi, vm.replaceInPreview);
                })
                .on('renderLate', function () {

                    while (vm._children.length) {
                        vm._children[0].$destroy();
                    }

                    Vue.nextTick(function() {
                        vm.$compile(editor.preview[0]);
                    });

                });


            editor.debouncedRedraw();
        },

        openModal: function(video) {

            var editor = this.editor,
                cursor = editor.editor.getCursor(),
                options = _.extend({ root: '/storage' }, this.options.finder);

            if (!video) {
                video = {
                    replace: function (value) {
                        editor.editor.replaceRange(value, cursor);
                    }
                };
            }

            this
                .$addChild({
                    data: {
                        video: _.extend({ src: '' }, video),
                        finder: { root: options.root.replace(/^\/+|\/+$/g, '')+'/' }
                    }
                }, Picker)
                .$mount()
                .$appendTo('body')
                .$on('select', function (video) {
                    video.replace('(video)' + JSON.stringify({src: video.src}));
                });
        },

        replaceInPreview: function(data) {

            var settings;

            try {

                settings = JSON.parse(data.matches[1]);

            } catch (e) {}

            $.extend(data, settings || { src: '' });

            return '<video-preview></video-preview>';
        },

        preview: function (url) {

            var code, matches;

            if ((matches = url.match(/(?:\/\/.*?youtube\.[a-z]+)\/watch\?v=([^&]+)&?(.*)/))
                || (matches = url.match(/youtu\.be\/(.*)/))
            ) {

                code = '<img src="//img.youtube.com/vi/' + matches[1] + '/hqdefault.jpg" class="uk-width-1-1">';

            } else if (url.match(/(\/\/.*?)vimeo\.[a-z]+\/([0-9]+).*?/)) {

                var id = btoa(url), session = sessionStorage || {};

                if (session[id]) {
                    code = '<img src="' + session[id] + '" class="uk-width-1-1">';
                } else {
                    code = '<img data-imgid="' + id + '" src="" class="uk-width-1-1">';

                    $.ajax({
                        type: 'GET',
                        url: 'http://vimeo.com/api/oembed.json?url=' + encodeURI(url),
                        jsonp: 'callback',
                        dataType: 'jsonp',
                        success: function (data) {
                            session[id] = data.thumbnail_url;
                            $('img[data-id="' + id + '"]').replaceWith('<img src="' + session[id] + '" class="uk-width-1-1">');
                        }
                    });
                }
            }

            console.log(url);
            console.log(Vue.url(url));

            return code ? code : '<video class="uk-width-1-1" src="' + (url ? Vue.url(url) : '') + '"></video>';
        }

    },

    components: {

        'video-preview': require('./preview.vue')

    }

};
