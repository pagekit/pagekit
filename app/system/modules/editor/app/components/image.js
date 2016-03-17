/**
 * Editor Image plugin.
 */

module.exports = {

    plugin: true,

    created: function () {

        var vm = this, editor = this.$parent.editor;

        if (!editor || !editor.htmleditor) {
            return;
        }

        this.images = [];

        editor
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

                while (vm.$children.length) {
                    vm.$children[0].$destroy();
                }

                Vue.nextTick(function () {
                    editor.preview.find('image-preview').each(function () {
                        vm.$compile(this);
                    });
                });
            });
    },

    methods: {

        openModal: function (image) {

            var editor = this.$parent.editor, cursor = editor.editor.getCursor();

            if (!image) {
                image = {
                    replace: function (value) {
                        editor.editor.replaceRange(value, cursor);
                    }
                };
            }

            new this.$parent.$options.utils['image-picker']({
                parent: this,
                data: {
                    image: image
                }
            }).$mount()
                .$appendTo('body')
                .$on('select', function (image) {

                    var content;

                    if ((image.tag || editor.getCursorMode()) == 'html') {

                        if (!image.anchor) {
                            image.anchor = $('<img>');
                        }

                        image.anchor.attr('src', image.data.src);
                        image.anchor.attr('alt', image.data.alt);

                        content = image.anchor[0].outerHTML;

                    } else {
                        content = '![' + image.data.alt + '](' + image.data.src + ')';
                    }

                    image.replace(content);
                });
        },

        replaceInPreview: function (data, index) {

            data.data = {};
            if (data.matches[0][0] == '<') {
                data.anchor = $(data.matches[0]);
                data.data.src = data.anchor.attr('src');
                data.data.alt = data.anchor.attr('alt');
                data.tag = 'html';
            } else {
                data.data.src = data.matches[3];
                data.data.alt = data.matches[2];
                data.tag = 'gfm';
            }

            return '<image-preview index="' + index + '"></image-preview>';
        }

    },

    components: {

        'image-preview': require('./image-preview.vue')

    }

};
