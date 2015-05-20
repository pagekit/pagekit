<template>

    <ul class="uk-nestable uk-form" v-el="nestable">

        <widget-item v-repeat="widget: position.widgets" v-show="widget | showWidget"></widget-item>

    </ul>

</template>

<script>

    module.exports = {

        inherit: true,

        ready: function() {
            var self = this;

            UIkit.nestable(this.$$.nestable, { maxDepth: 1, group: 'widgets' }).element.on('change.uk.nestable', function (e, el, type, root, nestable) {
                if (type !== 'removed' && e.target.tagName == 'UL') {
                    self.reorder(self.position.id, nestable.list());
                }
            });
        },

        components: {

            'widget-item': require('./item.vue')

        }

    };

</script>
