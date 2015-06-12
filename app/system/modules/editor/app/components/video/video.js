/**
 * Editor Video plugin.
 */

var Picker = require('./picker.vue');

module.exports = {

    plugin: {
        name: 'video'
    },

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

    methods: {

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

            _.merge(data, settings || { src: '' });

            return '<video-preview></video-preview>';
        }

    },

    components: {

        'video-view': require('./view.vue'),
        'video-preview': require('./preview.vue')

    }

};
