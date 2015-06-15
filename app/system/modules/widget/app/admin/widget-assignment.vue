<template>

    <div class="uk-form-row" v-repeat="menu: menus">
        <label for="form-h-it" class="uk-form-label">{{ menu.label }}</label>
        <div class="uk-form-controls uk-form-controls-text">
            <ul class="uk-list uk-margin-top-remove">
                <li v-partial="#node-item" v-repeat="node: menu.getNodes()"></li>
            </ul>
        </div>
    </div>

    <script id="node-item" type="text/template">

        <label>
            <input type="checkbox" value="{{ node.id }}" v-checkbox="widget.nodes">
            {{ node.title }}
        </label>

        <ul class="uk-list" v-if="menu.getNodes(node)">
            <li v-partial="#node-item" v-repeat="node: menu.getNodes(node)"></li>
        </ul>

    </script>

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
        }

    }

</script>
