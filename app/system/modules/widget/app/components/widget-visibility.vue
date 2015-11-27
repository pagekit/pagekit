<template>

    <div class="uk-form-horizontal">

        <div class="uk-form-row">
            <span class="uk-form-label">Pages</span>
            <div class="uk-form-controls uk-form-controls-text" v-if="menus">
                <p><strong>{{ all ? 'All Pages' : 'Only selected pages' | trans }}</strong></p>
                <ul class="uk-list uk-margin-top-remove" v-for="menu in menus" v-if="menu.getNodes().length">
                    <li class="pk-list-header">{{ menu.label }}</li>
                    <node v-for="node in menu.getNodes()" :widget.sync="widget" :node="node" :menu="menu"></node>
                </ul>
            </div>
        </div>

    </div>

</template>

<script>

    module.exports = {

        section: {
            label: 'Visibility',
            priority: 100
        },

        data: function () {
          return {
              menus: false
          }
        },

        props: ['widget', 'config'],

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

        computed: {

            all: function () {
                return !this.widget.nodes || !this.widget.nodes.length;
            }

        },

        components: {

            node: {

                props: ['widget', 'node', 'menu'],

                name: 'node',

                template:
                        '<li>'+
                            '<label>' +
                                '<input type="checkbox" :value="node.id" v-model="widget.nodes" number>' +
                                ' {{ node.title }}' +
                            '</label>' +
                            '<ul class="uk-list" v-if="menu.getNodes(node)">' +
                                '<node v-for="node in menu.getNodes(node)" :widget.sync="widget" :node="node" :menu="menu"></node>' +
                            '</ul>'+
                        '<li>'

            }

        }

    }

</script>
