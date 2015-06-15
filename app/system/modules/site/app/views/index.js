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
        menus: require('../components/menus.vue'),
        nodes: require('../components/nodes.vue')
    }

};

$(function () {

    new Vue(module.exports).$mount('#site');

});
