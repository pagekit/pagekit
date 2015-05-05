/*global $data*/

jQuery(function ($) {

    Vue.validators['unique'] = function(value) {
        var menu = _.find(this.menus, { id: value });
        return !menu || this.menu.oldId == menu.id;
    };

    Vue.http.options = _.extend({}, Vue.http.options, { error: function (msg) {
        UIkit.notify(msg, 'danger');
    }});

    var vm = new Vue({

        el: '#site',

        mixins: [require('./tree')],

        data: _.merge({ selected: null }, $data),

        events: {

            loaded: 'select'

        },

        methods: {

            select: function(node) {

                if (!node) {
                    node = this.selected && _.find(this.nodes, { id: vm.selected.id }) || this.selectFirst();
                }

                this.$set('selected', node);
            },

            selectFirst: function() {
                var first = null;
                this.menus.some(function (menu) {
                    return first = _.first(vm.tree[menu.id]);
                });

                return first ? first.node : undefined;
            }

        },

        components: {
            'menu-list': require('./components/menus.vue'),
            'node-edit': require('./components/edit.vue')
        }

    });

});
