<template>

    <ul class="uk-list uk-margin-top-remove" v-for="menu in menus" v-if="hasNodes(menu)">
    </pre>
        <li class="pk-list-header">{{ menu.label }}</li>
        <partial name="node-partial" v-for="node in getSubNodes(menu, node)"></partial>
    </ul>
</template>

<script>

    var Promise = require('promise');

    module.exports = {

        props: ['nodes'],

        replace: true,

        data: function () {
            return {
                'menus': [],
                'allnodes': []
            }
        },

        activate: function (done) {

            var vm = this;

            Promise
                .all([
                    this.$http.get('api/site/node'),
                    this.$http.get('api/site/menu')
                ])
                .then(function(responses) {
                    vm.allnodes  = responses[0].data;
                    vm.menus     = responses[1].data;
                    done();
                })
                .catch(function () {
                    vm.$notify('Could not load config.', 'danger');
                });
        },

        methods: {

            hasNodes: function (menu) {

                var grouped = _(this.allnodes).groupBy('menu').value();
                return (grouped[menu.id].length > 0);

            },

            getNodesForMenu: function (menu) {

                var grouped = _(this.allnodes).groupBy('menu').value();
                var sorted = _(grouped[menu.id] || {}).sortBy('priority').groupBy('parent_id').value();
                return sorted;

            },

            getSubNodes: function(menu, node) {

                var nodes = this.getNodesForMenu(menu);
                return (node ? nodes[node.id] : nodes[0]) || [];

            },

        },

        partials: {

            'node-partial': '<li>'+
                '<label>' +
                    '<input type="checkbox" :value="node.id" v-model="nodes" number>' +
                    ' {{ node.title }}' +
                '</label>' +
                '<partial name="node-partial" v-for="node in getSubNodes(menu, node)"></partial>' +
            '<li>'

        }
    }

    window.Vue.component('input-tree', function (resolve) {
        resolve(module.exports);
    });

</script>
