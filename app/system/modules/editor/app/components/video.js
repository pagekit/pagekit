/**
 * Editor Video plugin.
 */

var Picker = Vue.extend(require('./video-picker.vue'));

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
                vm.videos = editor.replaceInPreview(/\(video\)(\{.+?})/gi, vm.replaceInPreview);
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

            new Picker({
                parent: this,
                data: {
                    video: video
                }
            }).$mount()
                .$appendTo('body')
                .$on('select', function (video) {
                    video.replace('(video)' + JSON.stringify(video.data));
                });
        },

        replaceInPreview: function (data, index) {

            var settings;

            try {

                settings = JSON.parse(data.matches[1]);

            } catch (e) {
            }

            data.data = settings || {src: ''};

            return '<video-preview index="' + index + '"></video-preview>';
        }

    },

    components: {

        'video-preview': require('./video-preview.vue')

    }

};
