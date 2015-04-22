<div v-show="tree[menu.id].length" v-repeat="menu: menus" class="uk-form-row">
    <label for="form-h-it" class="uk-form-label">{{ menu.label }} {{ 'Menu' | trans }}</label>
    <div class="uk-form-controls uk-form-controls-text">

        <ul class="uk-list uk-margin-top-remove">
            <li v-partial="#node-item" v-repeat="item: tree[menu.id]"></li>
        </ul>
    </div>
</div>

<script id="node-item" type="text/template">

    <label>
        <input type="checkbox" value="{{ item.node.id }}" v-checkbox="widget.nodes">
        {{ item.node.title }}
    </label>

    <ul v-if="item.children.length" class="uk-list">
        <li v-partial="#node-item" v-repeat="item: item.children"></li>
    </ul>

</script>
