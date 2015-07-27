/**
 * Editor Video plugin.
 */

var Picker = Vue.extend(require('./video-picker.vue'));

module.exports = {

    plugin: true,

    created: function () {

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

        editor
            .on('action.video', function(e, editor) {
                vm.openModal(_.find(vm.videos, function(vid) {
                    return vid.inRange(editor.getCursor());
                }));
            })
            .on('render', function() {
                vm.videos = editor.replaceInPreview(/\(video\)(\{.+?})/gi, vm.replaceInPreview);
            })
            .on('renderLate', function () {

                while (vm.$children.length) {
                    vm.$children[0].$destroy();
                }

                Vue.nextTick(function() {
                    vm.$compile(editor.preview[0]);
                });

            });


        editor.debouncedRedraw();
    },

    methods: {

        openModal: function(video) {

            var editor = this.editor, cursor = editor.editor.getCursor();

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
                        video: video
                    }
                }, Picker)
                .$mount()
                .$appendTo('body')
                .$on('select', function (video) {
                    video.replace('(video)' + JSON.stringify({src: video.src}));
                });
        },

        replaceInPreview: function(data, index) {

            var settings;

            try {

                settings = JSON.parse(data.matches[1]);

            } catch (e) {}

            _.merge(data, settings || { src: '' });

            return '<video-preview index="'+index+'"></video-preview>';
        }

    },

    components: {

        'video-preview': require('./video-preview.vue')

    }

};
