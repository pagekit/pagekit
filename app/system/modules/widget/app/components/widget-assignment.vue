<template>

    <div class="uk-form-horizontal">

        <div class="uk-form-row">
            <label class="uk-form-label">Pages</label>
            <div class="uk-form-controls uk-form-controls-text">
                <ul class="uk-list uk-margin-top-remove">
                    <li><label><input type="checkbox" v-model="all" disabled> {{'All Pages'}}</label></li>
                </ul>
                <ul class="uk-list uk-margin-top-remove" v-repeat="menu: menus" v-show="menu.getNodes().length">
                    <li class="uk-nav-header">{{ menu.label }}</li>
                    <node v-repeat="node: menu.getNodes()"></node>
                </ul>
            </div>
        </div>

    </div>

</template>

<script>

    module.exports = {

        section: {
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
