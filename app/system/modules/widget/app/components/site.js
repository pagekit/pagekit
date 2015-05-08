var Site = require('site');
var Widgets = require('./index/index.vue');

Site.register(Widgets.extend({

    name: 'widgets',
    label: 'Widgets',
    priority: 20

}).options);
