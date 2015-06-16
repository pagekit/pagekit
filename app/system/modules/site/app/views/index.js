module.exports = Vue.extend({

    data: function() {
        return _.merge({ menu: undefined }, window.$data);
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

});

$(function () {

    (new module.exports()).$mount('#site');

});
