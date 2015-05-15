var $ = require('jquery');
var Vue = require('vue');
var finder = require('./components/finder.vue');

Vue.component('v-finder', $.extend({}, finder));

var Finder = function (element, options) {
    return new Vue($.extend(true, {}, finder, { el: element, data: $.extend(finder.data(), options)} ));
};

$(function () {
    $('[data-finder]').each(function () {
        new Finder(this, $(this).data('finder'));
    });
});

window.Finder = window.Finder || Finder;
