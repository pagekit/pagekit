<template>
    <ul class="uk-list uk-margin-top-remove" v-for="menu in getMenus()" v-if="menu.getNodes().length">
        <li class="pk-list-header">{{ menu.label }}</li>
        <partial name="node-partial" v-for="node in menu.getNodes()"></partial>
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

            getMenus: function () {

                var nodes = _(this.allnodes).groupBy('menu').value();
                
                return  _.mapValues(this.menus, function (menu) {

                    return _.extend(menu, {

                        nodes: _(nodes[menu.id] || {}).sortBy('priority').groupBy('parent_id').value(),

                        getNodes: function (node) {
                            return (node ? this.nodes[node.id] : this.nodes[0]) || [];
                        }
                    });
                });
            },
        },

        partials: {

            'node-partial': '<li>'+
                '<label>' +
                    '<input type="checkbox" :value="node.id" v-model="nodes" number>' +
                    ' {{ node.title }}' +
                '</label>' +
                '<partial name="node-partial" v-for="node in menu.getNodes(node)"></partial>' +
            '<li>'

        }
    }

    window.Vue.component('input-tree', function (resolve) {
        resolve(module.exports);
    });

</script>
