var $ = require('jquery');
var Vue = require('vue');
var Editor = require('./components/editor.vue');

Vue.component('v-editor', Editor);

$(function () {
    $('[data-editor]').each(function () {
        Editor.create(this, $(this).data('editor'));
    });
});

module.exports = Editor;
