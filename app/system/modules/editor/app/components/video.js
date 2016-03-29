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
                vm.videos = editor.replaceInPreview(/<(video|iframe)([^>]*)>[^<]*<\/(?:video|iframe)>|\(video\)(\{.+?})/gi, vm.replaceInPreview);
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

            var parser = new DOMParser(), editor = this.$parent.editor, cursor = editor.editor.getCursor();

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

                    var attributes, src, match;

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
                            video.anchor = parser.parseFromString('<iframe></iframe>', "text/html").body.childNodes[0];
                        }

                        _.forEach(video.data, function (value, key) {
                            if (key === 'src' || key === 'width' || key === 'height') {
                                return;
                            }

                            src += key + '=' + (_.isBoolean(value) ? Number(value) : value) + '&';
                        });

                        video.attributes = video.attributes || {};

                        video.attributes.src = src.slice(0, -1);
                        video.attributes.width = video.data.width || 690;
                        video.attributes.height = video.data.height || 390;
                        video.attributes.allowfullscreen = true;

                        attributes = video.attributes;

                    } else {

                        if (!video.anchor) {
                            video.anchor = parser.parseFromString('<video></video>', "text/html").body.childNodes[0];
                        }

                        attributes = video.data;

                    }


                    _.forEach(attributes, function (value, key) {
                        if (value) {
                            video.anchor.setAttribute(key, _.isBoolean(value) ? '' : value);
                        } else {
                            video.anchor.removeAttribute(key);
                        }
                    });

                    video.replace(video.anchor.outerHTML);

                });
        },

        replaceInPreview: function (data, index) {

            var parser = new DOMParser(), settings, src, query;

            if (!data.matches[3]) {

                data.data = {};
                data.anchor = parser.parseFromString(data.matches[0], "text/html").body.childNodes[0];

                if (data.anchor.nodeName === 'VIDEO') {

                    _.forEach(data.anchor.attributes, function (attr) {
                        data.data[attr.name] = attr.nodeValue === '' || attr.nodeValue;
                    });

                    data.data['controls'] = data.data['controls'] !== undefined;

                } else if (data.anchor.nodeName === 'IFRAME') {

                    data.attributes = {};
                    _.forEach(data.anchor.attributes, function (attr) {
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
            } else {

                try {
                    settings = JSON.parse(data.matches[3]);
                } catch (e) {}

                data.data = settings || {src: ''};
            }

            return '<video-preview index="' + index + '"></video-preview>';

        }

    },

    components: {

        'video-preview': require('./video-preview.vue')

    }

};
