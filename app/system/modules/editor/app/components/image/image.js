/**
 * Image plugin
 */

var $ = jQuery;

var ImageVm = {

    el: '#editor-image',

    data: {
        view: 'settings',
        style: '',
        image: {src: '', alt: ''},
        finder: {root: '', select: ''}
    },

    ready: function () {

        var vm = this;

        this.$on('select.finder', function(selected) {
            if (selected.length == 1 && selected[0].match(/\.(png|jpg|jpeg|gif|svg)$/i)) {
                vm.finder.select = selected[0];
            } else {
                vm.finder.select = '';
            }
        });

        this.$watch('image.src', this.preview);
        this.preview();
    },

    methods: {

        update: function () {

            var img = this.image;

            img.replace(img.tag.template({src: img.src, alt: img.alt}));
        },

        preview: function () {

            var vm = this, img = new Image(), src = '';

            if (this.image.src) {
                src = this.$url.static(this.image.src);
            }

            img.onerror = function() {
                vm.style = '';
            };

            img.onload  = function() {
                vm.style = 'background-image: url("' + src + '"); background-size: contain';
            };

            img.src = src;
        },

        openFinder: function () {
            this.view = 'finder';
            this.finder.select = '';
        },

        closeFinder: function (select) {
            this.view = 'settings';
            if (select) this.image.src = select;
        }

    }

};

UIkit.plugin('htmleditor', 'image', {

    init: function(editor) {

        var self = this;

        this.editor = editor;
        this.images = [];

        editor.element.off('action.image');
        editor.element.on('action.image', function() {

            var cursor = editor.editor.getCursor(), image;

            self.images.every(function(img) {

                if (img.inRange(cursor)) {
                    image = img;
                    return false;
                }

                return true;
            });

            self.openModal(image);
        });

        editor.element.on('render', function() {
            var regexp = editor.getMode() != 'gfm' ? /<img(.+?)>/gi : /(?:<img(.+?)>|!(?:\[([^\n\]]*)\])(?:\(([^\n\]]*)\))?)/gi;
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

        var editor = this.editor, cursor = editor.editor.getCursor(), vm = $.extend(true, {}, ImageVm), modal;
        var options = editor.element.data('finder-options'), root = options.root.replace(/^\/+|\/+$/g, '')+'/';

        if (!image) {
            image = {
                tag: editor.getCursorMode() == 'html' ? '<img src="${src}" alt="${alt}">' : '![${alt}](${src})',
                replace: function (value) {
                    editor.editor.replaceRange(value, cursor);
                }
            };
        }

        modal = $(require('./modal.html')).appendTo('body');
        modal.on('hide.uk.modal', function() {
            $(this).remove();
        });

        UIkit.modal(modal).show();

        $.extend(vm.data.image, image);
        vm.data.finder.root = root;
        vm = new Vue(vm);
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

        return templates['image.replace'].template({src: data.src, alt: data.alt}).replace(/(\r\n|\n|\r)/gm, '');
    }

});
