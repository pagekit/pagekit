<template>

    <li class="uk-nestable-item" v-class="uk-parent: isParent, uk-active: isActive" data-id="{{ node.id }}">

        <div class="uk-nestable-panel uk-visible-hover-inline" v-on="click: select(node)">
            <div class="uk-nestable-handle"></div>
            <div class="uk-nestable-toggle" data-nestable-action="toggle"></div>
            {{ node.title }}

            <i class="uk-float-right uk-icon-home" title="{{ 'Frontpage' | trans }}" v-show="node.data.frontpage"></i>
            <a class="uk-hidden uk-float-right" title="{{ 'Delete' | trans }}" v-on="click: delete"><i class="uk-icon-minus-circle"></i></a>
        </div>

        <ul class="uk-nestable-list" v-if="isParent">
            <node-item v-repeat="node: tree[node.id]"></node-item>
        </ul>

    </li>

</template>

<script>

    module.exports = {

        inherit: true,
        replace: true,

        computed: {

            isActive: function() {
                return this.node === this.selected;
            },

            isParent: function() {
                return this.tree[this.node.id];
            }

        },

        methods: {

            'delete': function(e) {

                e.preventDefault();
                e.stopPropagation();

                this.Nodes.delete({ id: this.node.id }, this.load);
            }

        }

    }

</script>
