var $ = require('jquery');
var _ = require('lodash');
var UIkit = require('uikit');

Vue.validators['unique'] = function(value) {
    var menu = _.find(this.menus, { id: value });
    return !menu || this.menu.oldId == menu.id;
};

Vue.http.options = _.extend({}, Vue.http.options, { error: function (msg) {
    UIkit.notify(msg, 'danger');
}});

var Site = Vue.extend({

        mixins: [require('./tree')],

        data: function() {
            return _.merge({ selected: null }, window.$data);
        },

        events: {

            loaded: 'select'

        },

        methods: {

            select: function(node) {

                if (!node) {
                    node = this.selected && _.find(this.nodes, { id: this.selected.id }) || this.selectFirst();
                }

                this.$set('selected', node);
            },

            selectFirst: function() {
                var self = this, first = null;
                this.menus.some(function (menu) {
                    return first = _.first(self.tree[menu.id]);
                });

                return first ? first.node : undefined;
            }

        },

        components: {
            'menu-list': require('./components/menus.vue'),
            'node-edit': require('./components/edit.vue')
        }

    });

$(function () {

    var site = new Site();
    site.$mount('#site');

});

module.exports = Site;
