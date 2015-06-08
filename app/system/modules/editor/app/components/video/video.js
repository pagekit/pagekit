/**
 * Editor Video plugin.
 */

var $ = require('jquery');
var _ = require('lodash');
var Vue = require('vue');
var UIkit = require('uikit');
var Picker = require('./picker.vue');

UIkit.plugin('htmleditor', 'video', {

    init: function(editor) {

        var self = this;

        this.editor = editor;
        this.videos = [];

        editor.addButton('video', {
            title: 'Video',
            label: '<i class="uk-icon-video-camera"></i>'
        });

        editor.element.on('action.video', function(e, editor) {
            self.openModal(_.find(self.videos, function(vid) {
                return vid.inRange(editor.getCursor());
            }));
        });

        editor.options.toolbar.push('video');

        editor.element.on('render', function() {
            self.videos = editor.replaceInPreview(/\(video\)(\{.+?})/gi, self.replaceInPreview);
        });

        editor.preview.on('click', '.js-editor-video .js-config', function() {
            var index = editor.preview.find('.js-editor-video .js-config').index(this);
            self.openModal(self.videos[index]);
        });

        editor.preview.on('click', '.js-editor-video .js-remove', function() {
            var index = editor.preview.find('.js-editor-video .js-remove').index(this);
            self.videos[index].replace('');
        });

        return editor;
    },

    openModal: function(video) {

        var editor = this.editor, cursor = editor.editor.getCursor(),
            options = editor.element.data('finder-options'),
            root = options.root.replace(/^\/+|\/+$/g, '')+'/';

        if (!video) {
            video = {
                src: '',
                replace: function (value) {
                    editor.editor.replaceRange(value, cursor);
                }
            };
        }

        var vm = new Picker();

        vm.$on('select', function(vid) {
            vid.replace('(video)' + JSON.stringify({src: vid.src}));
        });
        vm.$set('video', $.extend(vm.$get('video'), video));
        vm.$set('finder.root', root);
        vm.$mount().$appendTo('body');
    },

    replaceInPreview: function(data) {

        var settings;

        try {

            settings = JSON.parse(data.matches[1]);

        } catch (e) {}

        $.extend(data, settings || { src: '' });

        return $('#editor-video-replace').text().template({src: data.src, preview: Picker.options.methods.preview(data.src)}).replace(/(\r\n|\n|\r)/gm, '');
    }

});
