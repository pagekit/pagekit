/**
 * Editor Image plugin.
 */

var Picker = require('./picker.vue');

module.exports = {

    plugin: {
        name: 'image'
    },

    created: function () {

        var vm = this, editor = this.editor;

        if (!editor || !editor.htmleditor) {
            return;
        }

        this.images = [];

        editor.element
            .off('action.image')
            .on('action.image', function (e, editor) {
                vm.openModal(_.find(vm.images, function (img) {
                    return img.inRange(editor.getCursor());
                }));
            })
            .on('render', function () {
                var regexp = editor.getMode() != 'gfm' ? /<img(.+?)>/gi : /(?:<img(.+?)>|!(?:\[([^\n\]]*)])(?:\(([^\n\]]*?)\))?)/gi;
                vm.images = editor.replaceInPreview(regexp, vm.replaceInPreview);
            })
            .on('renderLate', function () {

                while (vm._children.length) {
                    vm._children[0].$destroy();
                }

                Vue.nextTick(function() {
                    vm.$compile(editor.preview[0]);
                });

            });

    },

    methods: {

        openModal: function (image) {

            var editor = this.editor,
                cursor = editor.editor.getCursor(),
                options = _.extend({ root: '/storage' }, this.options.finder);

            if (!image) {
                image = {
                    replace: function (value) {
                        editor.editor.replaceRange(value, cursor);
                    }
                };
            }

            this.$addChild({
                    data: {
                        image: _.extend({ src: '', alt: '' }, image),
                        finder: { root: options.root.replace(/^\/+|\/+$/g, '') + '/' }
                    }
                }, Picker)
                .$mount()
                .$appendTo('body')
                .$on('select', function (image) {
                    image.replace(this.$interpolate(
                        (image.tag || editor.getCursorMode()) == 'html' ?
                            '<img src="{{ image.src }}" alt="{{ image.alt }}">'
                            : '![{{ image.alt }}]({{ image.src }})'
                        )
                    );
                });
        },

        replaceInPreview: function (data) {

            if (data.matches[0][0] == '<') {
                data.src = data.matches[0].match(/src="(.*?)"/)[1];
                data.alt = data.matches[0].match(/alt="(.*?)"/)[1];
                data.tag = 'html';
            } else {
                data.src = data.matches[3];
                data.alt = data.matches[2];
                data.tag = 'gfm';
            }

            return '<image-preview></image-preview>';
        }

    },

    components: {

        'image-preview': require('./preview.vue')

    }

};
