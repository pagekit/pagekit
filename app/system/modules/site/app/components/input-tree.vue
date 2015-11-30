<template>

    <ul class="uk-list uk-margin-top-remove" v-for="menu in menus" v-if="menu.getNodes().length">
        <li class="pk-list-header">{{ menu.label }}</li>
        <node v-for="node in menu.getNodes()" :nodes.sync="nodes" :node="node" :menu="menu"></node>
    </ul>

</template>

<script>

    module.exports = {

        props: ['config', 'nodes'],

        ready: function () {

            var nodes = _(this.config.nodes).groupBy('menu').value();

            this.$set('menus', _.mapValues(this.config.menus, function (menu, id) {

                return _.extend(menu, {

                    nodes: _(nodes[id] || {}).sortBy('priority').groupBy('parent_id').value(),

                    getNodes: function (node) {
                        return (node ? this.nodes[node.id] : this.nodes[0]) || [];
                    }

                });
            }));

        },

        components: {

            node: {

                props: ['nodes', 'node', 'menu'],

                name: 'node',

                template: '<li>'+
                    '<label>' +
                        '<input type="checkbox" :value="node.id" v-model="nodes" number>' +
                        ' {{ node.title }}' +
                    '</label>' +
                    '<ul class="uk-list" v-if="menu.getNodes(node)">' +
                        '<node v-for="node in menu.getNodes(node)" :nodes.sync="nodes" :node="node" :menu="menu"></node>' +
                    '</ul>'+
                '<li>'

            }
        }
    }

</script>
