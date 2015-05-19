<template>

    <div v-show="tree[menu.id].length" v-repeat="menu: menus" class="uk-form-row">
        <label for="form-h-it" class="uk-form-label">{{ menu.label }} {{ 'Menu' | trans }}</label>
        <div class="uk-form-controls uk-form-controls-text">

            <ul class="uk-list uk-margin-top-remove">
                <li v-partial="#node-item" v-repeat="node: tree[menu.id]"></li>
            </ul>
        </div>
    </div>

    <script id="node-item" type="text/template">

        <label>
            <input type="checkbox" value="{{ node.id }}" v-checkbox="widget.nodes">
            {{ node.title }}
        </label>

        <ul v-if="tree[node.id].length" class="uk-list">
            <li v-partial="#node-item" v-repeat="node: tree[node.id]"></li>
        </ul>

    </script>

</template>

<script>

    module.exports = {

        name: 'assignment',
        label: 'Assignment',
        priority: 100,
        template: __vue_template__,

        mixins: [require('../../../../site/app/tree.js')]

    }

</script>
