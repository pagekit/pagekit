var $ = require('jquery');
var Vue = require('vue');

$(function () {

  var opts = require('./components/themes');
  var app  = new Vue(opts).$mount('#themes');

});
