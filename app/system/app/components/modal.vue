<template>

    <div class="uk-modal">
        <div class="uk-modal-dialog{{ modifier ? ' '+modifier : '' }}" v-class="'uk-modal-dialog-large': large, 'uk-modal-dialog-lightbox': lightbox">
            <div v-if="opened">
                <content></content>
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
            modifier: String
        },

        ready: function () {

            var vm = this;

            this.$appendTo('body');

            this.modal = UIkit.modal(this.$el, {modal: false});
            this.modal.on('hide.uk.modal', function () {

                vm.opened = false;

                if (vm.closed) {
                    vm.closed();
                }
            });
        },

        methods: {

            open: function (data) {
                this.opened = true;
                this.modal.show();
            },

            close: function () {
                this.modal.hide();
            }

        }

    };

</script>
