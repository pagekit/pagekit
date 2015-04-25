jQuery(function ($) {

  $('body').append('<div id="profiler"></div>');

  var opts = require('./debugbar.vue');
  var app  = new Vue(opts).$mount('#profiler');

});
