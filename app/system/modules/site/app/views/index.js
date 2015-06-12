var $ = require('jquery');
var _ = require('lodash');
var UIkit = require('uikit');

var Site = Vue.extend({

    data: function() {
        return _.merge({}, window.$data);
    },

    http: {

        error: function (msg) {
            UIkit.notify(msg, 'danger');
        }

    },

    components: {
        'menu-list': require('../components/menus.vue'),
        'node-list': require('../components/nodes.vue')
    }

});

$(function () {

    new Site().$mount('#site');

});

module.exports = Site;
