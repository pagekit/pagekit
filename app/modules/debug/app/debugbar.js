var $ = require('jquery');
var Vue = require('vue');

$(function () {

  $('body').append('<div id="profiler"></div>');

  new Vue(require('./debugbar.vue')).$mount('#profiler');

});
