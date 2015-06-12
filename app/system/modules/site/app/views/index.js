module.exports = {

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

};

$(function () {

    new Vue(module.exports).$mount('#site');

});
