<template>

    <ul class="uk-nestable">
        <node-item v-repeat="node: tree[menu.id]"></node-item>
    </ul>

</template>

<script>

    module.exports = {

        inherit: true,
        replace: true,

        ready: function () {

            var self = this;

            UIkit.nestable(this.$el, { maxDepth: 20, group: 'site.nodes' }).on('change.uk.nestable', function (e, el, type, root, nestable) {
                if (type !== 'removed') {
                    self.Nodes.save({ id: 'updateOrder' }, { menu: self.menu.id, nodes: nestable.list() }, function() {
                        self.load();

                        UIkit.notify(this.$trans('Order updated.'));
                    });
                }
            });

        },

        components: {

            'node-item': require('./item.vue')

        }
    }

</script>
