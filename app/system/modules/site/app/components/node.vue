<template>

    <li class="uk-nestable-item" v-class="uk-parent: tree[node.id]" data-id="{{ node.id }}">

        <div class="uk-nestable-panel pk-table-fake">
            <div class="pk-table-width-minimum"><div class="uk-nestable-handle"></div></div>
            <div class="pk-table-width-minimum"><div class="uk-nestable-toggle" data-nestable-action="toggle"></div></div>
            <div class="pk-table-width-minimum"><input type="checkbox" name="id" value="{{ node.id }}"></div>
            <div class="pk-table-min-width-100"><a v-attr="href: $url('admin/site/edit', { id: node.id })">{{ node.title }}</a></div>
            <div class="pk-table-width-100 uk-text-center">
                <td class="uk-text-center">
                    <a class="uk-icon-circle" v-class="
                            uk-text-success: node.status,
                            uk-text-danger: !node.status
                        " v-on="click: toggleStatus(node)"></a>
                </td>
            </div>
            <div class="pk-table-width-150 pk-table-max-width-150 uk-text-truncate">
                <a>{{ node.path }}</a>
            </div>


<!--            <i class="uk-float-right uk-icon-home" title="{{ 'Frontpage' | trans }}" v-show="node.data.frontpage"></i>-->
        </div>

        <ul class="uk-nestable-list" v-if="tree[node.id]">
            <node-item v-repeat="node: tree[node.id]"></node-item>
        </ul>

    </li>

</template>

<script>

    module.exports = {

        paramAttributes: ['node', 'tree'],
        inherit: true,
        replace: true

    }

</script>
