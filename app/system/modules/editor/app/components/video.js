/**
 * Editor Video plugin.
 */

module.exports = {

    plugin: true,

    created: function () {

        var vm = this, editor = this.$parent.editor;

        if (!editor || !editor.htmleditor) {
            return;
        }

        this.videos = [];

        editor.addButton('video', {
            title: 'Video',
            label: '<i class="uk-icon-video-camera"></i>'
        });

        editor.options.toolbar.push('video');

        editor
            .on('action.video', function (e, editor) {
                vm.openModal(_.find(vm.videos, function (vid) {
                    return vid.inRange(editor.getCursor());
                }));
            })
            .on('render', function () {
                vm.videos = editor.replaceInPreview(/<(video|iframe)([^>]*)>[^<]*<\/(?:video|iframe)>/gi, vm.replaceInPreview);
            })
            .on('renderLate', function () {

                while (vm.$children.length) {
                    vm.$children[0].$destroy();
                }

                Vue.nextTick(function () {
                    editor.preview.find('video-preview').each(function () {
                        vm.$compile(this);
                    });
                });

            });

        editor.debouncedRedraw();
    },

    methods: {

        openModal: function (video) {

            var editor = this.$parent.editor, cursor = editor.editor.getCursor();

            if (!video) {
                video = {
                    replace: function (value) {
                        editor.editor.replaceRange(value, cursor);
                    }
                };
            }

            var picker = new this.$parent.$options.utils['video-picker']({
                parent: this,
                data: {
                    video: video
                }
            }).$mount()
                .$appendTo('body')
                .$on('select', function (video) {

                    var src, match;

                    delete video.data.playlist;

                    if (match = picker.isYoutube) {
                        src = 'https://www.youtube.com/embed/' + match[1] + '?';

                        if (video.data.loop) {
                            video.data.playlist = match[1];
                        }
                    } else if (match = picker.isVimeo) {
                        src = 'https://player.vimeo.com/video/' + match[3] + '?';
                    }

                    if (src) {

                        if (!video.anchor) {
                            video.anchor = $('<iframe></iframe>');
                        }

                        _.forEach(video.data, function (value, key) {
                            if (key === 'src' || key === 'width' || key === 'height') {
                                return;
                            }

                            src += key + '=' + _.isBoolean(value) ? Number(value) : value + '&';
                        });

                        video.attributes = video.attributes || {};

                        video.attributes.src = src.slice(0, -1);
                        video.attributes.width = video.data.width || 690;
                        video.attributes.height = video.data.height || 390;
                        video.attributes.allowfullscreen = true;

                        _.forEach(video.attributes, function (value, key) {
                            video.anchor.attr(key, value);
                        });

                    } else {

                        if (!video.anchor) {
                            video.anchor = $('<video></video>');
                        }

                        _.forEach(video.data, function (value, key) {
                            video.anchor.attr(key, value);
                        });

                    }

                    video.replace(video.anchor[0].outerHTML);

                });
        },

        replaceInPreview: function (data, index) {

            var src, query;

            data.data = {};
            data.anchor = $(data.matches[0]);

            if (data.anchor[0].nodeName === 'VIDEO') {

                _.forEach(data.anchor[0].attributes, function (attr) {
                    data.data[attr.name] = attr.nodeValue === '' || attr.nodeValue;
                });

            } else if (data.anchor[0].nodeName === 'IFRAME') {

                data.attributes = {};
                _.forEach(data.anchor[0].attributes, function (attr) {
                    data.attributes[attr.name] = attr.nodeValue === '' || attr.nodeValue;
                });

                src = data.attributes.src || '';
                src = src.split('?');
                query = src[1] || '';
                src = src[0];
                query.split('&').forEach(function (param) {
                    param = param.split('=');
                    data.data[param[0]] = param[1];
                });

                data.data.src = src;
                if (data.attributes.width) {
                    data.data.width = data.attributes.width;
                }
                if (data.attributes.height) {
                    data.data.height = data.attributes.height;
                }

            }

            return '<video-preview index="' + index + '"></video-preview>';

        }

    },

    components: {

        'video-preview': require('./video-preview.vue')

    }

};
