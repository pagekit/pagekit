<template>

    <li class="uk-nestable-item" v-class="uk-parent: tree[node.id]" data-id="{{ node.id }}">

        <div class="uk-nestable-panel pk-table-fake">
            <div class="pk-table-width-minimum"><div class="uk-nestable-handle"></div></div>
            <div class="pk-table-width-minimum"><div class="uk-nestable-toggle" data-nestable-action="toggle"></div></div>
            <div class="pk-table-width-minimum"><input type="checkbox" name="id" value="{{ node.id }}"></div>
            <div class="pk-table-min-width-100">
                <a v-attr="href: $url('admin/site/edit', { id: node.id })">
                    {{ node.title }}
                </a>
                <i class="uk-float-right uk-icon-home" title="{{ 'Frontpage' | trans }}" v-show="node.id == frontpage"></i>
            </div>
            <div class="pk-table-width-100 uk-text-center">
                <td class="uk-text-center">
                    <a v-class="
                            pk-icon-circle-success: node.status,
                            pk-icon-circle-danger: !node.status
                        " v-on="click: toggleStatus(node)"></a>
                </td>
            </div>
            <div class="pk-table-width-150 pk-table-max-width-150 uk-text-truncate">
                <a>{{ node.path }}</a>
            </div>
        </div>

        <ul class="uk-nestable-list" v-if="tree[node.id]">
            <node v-repeat="node: tree[node.id]"></node>
        </ul>

    </li>

</template>

<script>

    module.exports = {

        inherit: true,
        replace: true

    }

</script>
