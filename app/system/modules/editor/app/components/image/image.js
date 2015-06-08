/**
 * Editor Image plugin.
 */

var $ = require('jquery');
var _ = require('lodash');
var Vue = require('vue');
var UIkit = require('uikit');
var Picker = require('./picker.vue');

UIkit.plugin('htmleditor', 'image', {

    init: function(editor) {

        var self = this;

        this.editor = editor;
        this.images = [];

        editor.element.off('action.image');
        editor.element.on('action.image', function(e, editor) {
            self.openModal(_.find(self.images, function(img) {
                return img.inRange(editor.getCursor());
            }));
        });

        editor.element.on('render', function() {
            var regexp = editor.getMode() != 'gfm' ? /<img(.+?)>/gi : /(?:<img(.+?)>|!(?:\[([^\n\]]*)])(?:\(([^\n\]]*?)\))?)/gi;
            self.images = editor.replaceInPreview(regexp, self.replaceInPreview);
        });

        editor.preview.on('click', '.js-editor-image .js-config', function() {
            var index = editor.preview.find('.js-editor-image .js-config').index(this);
            self.openModal(self.images[index]);
        });

        editor.preview.on('click', '.js-editor-image .js-remove', function() {
            var index = editor.preview.find('.js-editor-image .js-remove').index(this);
            self.images[index].replace('');
        });

        return editor;
    },

    openModal: function(image) {

        var editor = this.editor,
            cursor = editor.editor.getCursor(),
            options = editor.element.data('finder-options'),
            root = options.root.replace(/^\/+|\/+$/g, '')+'/';

        if (!image) {
            image = {
                tag: editor.getCursorMode() == 'html' ? '<img src="${src}" alt="${alt}">' : '![${alt}](${src})',
                replace: function (value) {
                    editor.editor.replaceRange(value, cursor);
                }
            };
        }

        var vm = new Picker();

        vm.$on('select', function(img) {
            img.replace(img.tag.template({src: img.src, alt: img.alt}));
        });
        vm.$set('image', $.extend(vm.$get('image'), image));
        vm.$set('finder.root', root);
        vm.$mount().$appendTo('body');
    },

    replaceInPreview: function(data) {

        if (data.matches[0][0] == '<') {

            if (data.matches[0].match(/js\-no\-parse/)) {
                return false;
            }

            var src = data.matches[0].match(/src="(.*?)"/), alt = data.matches[0].match(/alt="(.*?)"/);

            data.src = src ? src[1] : '';
            data.alt = alt ? alt[1] : '';
            data.tag = data.matches[0].replace(/src="(.*?)"/, 'src="${src}"').replace(/alt="(.*?)"/, 'alt="${alt}"');

        } else {
            data.src = data.matches[3].trim();
            data.alt = data.matches[2];
            data.tag = '![${alt}](${src})';
        }

        return $('#editor-image-replace').text().template({src: data.src, alt: data.alt}).replace(/(\r\n|\n|\r)/gm, '');
    }

});
