var $ = require('jquery');
var Vue = require('vue');

$(function () {

  var opts = require('./components/extensions');
  var app  = new Vue(opts).$mount('#extensions');

});
