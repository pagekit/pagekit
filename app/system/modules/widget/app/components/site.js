var Site = require('site');
var Widgets = require('./index/index.vue');

Site.component('widgets', Widgets.extend({

    section: {
        name: 'widgets',
        label: 'Widgets',
        priority: 20
    }

}));
