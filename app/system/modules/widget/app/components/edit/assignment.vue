<template>

    <div class="uk-form-row" v-show="$root.tree[menu.id].length" v-repeat="menu: $root.menus">
        <label for="form-h-it" class="uk-form-label">{{ menu.label }} {{ 'Menu' | trans }}</label>
        <div class="uk-form-controls uk-form-controls-text">
            <ul class="uk-list uk-margin-top-remove">
                <li v-partial="node-item" v-repeat="node: $root.tree[menu.id]"></li>
            </ul>
        </div>
    </div>

    <script id="node-item" type="text/template">

        <label>
            <input type="checkbox" value="{{ node.id }}" v-checkbox="widget.nodes">
            {{ node.title }}
        </label>

        <ul class="uk-list" v-if="$root.tree[node.id].length">
            <li v-partial="node-item" v-repeat="node: $root.tree[node.id]"></li>
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

        paramAttributes: ['widget'],

        partials: {

            'node-item': require('./item.html')

        }

    }

</script>
