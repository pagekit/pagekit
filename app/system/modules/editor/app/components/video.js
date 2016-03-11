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
                vm.videos = editor.replaceInPreview(/<video([^>]*)><\/video>/gi, vm.replaceInPreview);
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

                    var content, src, match;

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

                        Object.keys(video.data).forEach(function (attr) {
                            if (attr === 'src' || attr === 'width' || attr === 'height') {
                                return;
                            }

                            src += attr + '=' + video.data[attr] + '&';
                        });

                        video.attributes.src = src.slice(0, -1);
                        video.attributes.width = video.data.width;
                        video.attributes.height = video.data.height;
                        video.attributes.allowfullscreen = true;

                        content = '<iframe';
                        Object.keys(video.attributes).forEach(function (attr) {
                            content += ' ' + attr + ( _.isBoolean(video.attributes[attr]) ? '' : '="' + video.attributes[attr] + '"');
                        });

                        content += '></iframe>';

                    } else {

                        content = '<video';

                        Object.keys(video.data).forEach(function (attr) {
                            var value = video.data[attr];
                            content += ' ' + attr + (_.isBoolean(value) ? '' : '="' + value + '"');
                        });

                        content += '></video>';
                    }

                    video.replace(content);

                });
        },

        replaceInPreview: function (data, index) {

            var matches,
                regex = /([^=\s"']+)\s*=(?:"([^"]*)"|'([^']*)')|([^=\s"']+)/gi;

            data.data = {};
            while ((matches = regex.exec(data.matches[1])) !== null) {
                data.data[matches[1]] = matches[2] === undefined || matches[2];
            }

            return '<video-preview index="' + index + '"></video-preview>';

        }

    },

    components: {

        'video-preview': require('./video-preview.vue')

    }

};
