var Site = require('site');
var Widgets = require('./index/index.vue');

module.exports = Widgets.extend({

    section: {
        name: 'widgets',
        label: 'Widgets',
        priority: 20
    }

});

Site.component('widgets', module.exports);
