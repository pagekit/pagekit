<template>

    <div class="uk-modal">
        <div class="uk-modal-dialog" :class="classes">
            <div v-if="opened">
                <slot></slot>
            </div>
        </div>
    </div>

</template>

<script>

    module.exports = {

        data: function () {
            return {
                opened: false
            };
        },

        props: {
            large: Boolean,
            lightbox: Boolean,
            closed: Function,
            modifier: {type: String, default: ''},
            options: {
                type: Object, default: function () {
                    return {};
                }
            }
        },

        ready: function () {

            var vm = this;

            this.$appendTo('body');

            this.modal = UIkit.modal(this.$el, _.extend({modal: false}, this.options));
            this.modal.on('hide.uk.modal', function () {

                vm.opened = false;

                if (vm.closed) {
                    vm.closed();
                }
            });

        },

        computed: {

            classes: function () {
                var classes = this.modifier.split(' ');

                if (this.large) {
                    classes.push('uk-modal-dialog-large');
                }

                if (this.lightbox) {
                    classes.push('uk-modal-dialog-lightbox');
                }

                return classes;
            }

        },

        methods: {

            open: function () {
                this.opened = true;
                this.modal.show();
            },

            close: function () {
                this.modal.hide();
            }

        }

    };

</script>
