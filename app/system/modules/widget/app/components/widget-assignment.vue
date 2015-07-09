<template>

    <div class="uk-form-horizontal">

        <div class="uk-form-row" v-repeat="menu: menus" v-show="menu.getNodes().length">
            <label class="uk-form-label">{{ menu.label }}</label>
            <div class="uk-form-controls uk-form-controls-text">
                <ul class="uk-list uk-margin-top-remove">
                    <node v-repeat="node: menu.getNodes()"></node>
                </ul>
            </div>
        </div>

    </div>

</template>

<script>

    module.exports = {

        section: {
            name: 'assignment',
            label: 'Assignment',
            priority: 100
        },

        inherit: true,

        props: ['widget'],

        ready: function () {

            var nodes = _(this.config.nodes).groupBy('menu').value();

            this.menus = _.mapValues(this.config.menus, function (menu, id) {

                return _.extend(menu, {

                    nodes: _(nodes[id] || {}).sortBy('priority').groupBy('parentId').value(),

                    getNodes: function (node) {
                        return node ? this.nodes[node.id] : this.nodes[0];
                    }

                });
            });
        },

        components: {

            node: {

                inherit: true,

                template:
                        '<li>'+
                            '<label>' +
                                '<input type="checkbox" value="{{ node.id }}" v-checkbox="widget.nodes">' +
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
