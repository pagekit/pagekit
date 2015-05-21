<template>

    <div class="uk-modal" v-el="modal">

        <div class="uk-modal-dialog uk-modal-dialog-large">

            <widget-edit v-if="widget" widget="{{ widget }}" type="{{ type }}" config="{{ widgetConfig }}" position="{{ position }}"></widget-edit>

        </div>
    </div>

</template>

<script>

    module.exports = {

        inherit: true,

        created: function () {
            var container = document.createElement('div');
            document.body.appendChild(container);
            this.$mount(container);
        },

        ready: function() {

            var self = this;

            this.modal = UIkit.modal(this.$$.modal);
            this.modal.on('hide.uk.modal', function() {
                self.$set('widget', null);
            });

        },

        events: {

            saved: function() {
                this.cancel();
                this.load();
            },

            cancel: function() {
                this.cancel();
            }

        },

        watch: {

            widget: function (widget) {
                if (widget) {
                    this.modal.show();
                }
            }

        },

        computed: {

            type: function() {
                return _.find(this.config.types, { id: this.widget.type });
            },

            widgetConfig: function() {
                return _.defaults({}, this.config.configs[this.widget.id], this.config.configs.defaults);
            },

            position: function() {

                var id = this.widget.id;

                var position = _.find(this.positions, function(position) {
                    return _.find(position.widgets, { id: id });
                });

                return position && position.id;
            }

        },

        methods: {

            cancel: function() {
                this.modal.hide();
            }

        },

        components: {

            'widget-edit': require('./edit.vue')

        }

    };

</script>
