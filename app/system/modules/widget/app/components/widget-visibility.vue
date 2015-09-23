<template>

    <div class="uk-form-horizontal">

        <div class="uk-form-row">
            <span class="uk-form-label">Pages</span>
            <div class="uk-form-controls uk-form-controls-text">
                <p><strong>{{ all ? 'All Pages' : 'Only selected pages' | trans }}</strong></p>
                <ul class="uk-list uk-margin-top-remove" v-repeat="menu: menus" v-show="menu.getNodes().length">
                    <li class="pk-list-header">{{ menu.label }}</li>
                    <node v-repeat="node: menu.getNodes()"></node>
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

        inherit: true,

        props: ['widget'],

        ready: function () {

            var nodes = _(this.config.nodes).groupBy('menu').value();

            this.$set('menus', _.mapValues(this.config.menus, function (menu, id) {

                return _.extend(menu, {

                    nodes: _(nodes[id] || {}).sortBy('priority').groupBy('parent_id').value(),

                    getNodes: function (node) {
                        return node ? this.nodes[node.id] : this.nodes[0];
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

                inherit: true,

                template:
                        '<li>'+
                            '<label>' +
                                '<input type="checkbox" value="{{ node.id }}" v-checkbox="widget.nodes" number>' +
                                ' {{ node.title }}' +
                            '</label>' +
                            '<ul class="uk-list" v-if="menu.getNodes(node)">' +
                                '<node v-repeat="node: menu.getNodes(node)"></node>' +
                            '</ul>'+
                        '<li>'

            }

        }

    }

</script>
