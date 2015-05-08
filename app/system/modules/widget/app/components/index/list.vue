<template>

    <ul class="uk-nestable uk-form">

        <li data-id="{{ widget.id }}" v-repeat="widget: position.widgets" class="uk-nestable-list-item" v-show="widget | showWidget">

            <widget-item></widget-item>

        </li>

    </ul>

</template>

<script>

    module.exports = {

        inherit: true,

        ready: function() {
            var self = this;

            UIkit.nestable(this.$el, { maxDepth: 1, group: 'widgets' }).element.on('change.uk.nestable', function (e, el, type, root, nestable) {
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
