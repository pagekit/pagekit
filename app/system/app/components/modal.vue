<template>

    <div class="uk-modal">
        <div class="uk-modal-dialog" v-class="'uk-modal-dialog-large': large, 'uk-modal-dialog-lightbox': lightbox">
            <div v-if="opened">
                <content></content>
            </div>
        </div>
    </div>

</template>


<script>

    module.exports = {

        data: function () {
            return {opened: false};
        },

        props: {large: Boolean, lightbox: Boolean, closed: Function},

        created: function () {

            var options = this.$options, div;

            if (typeof options.template !== 'string') {

                div = document.createElement('div');
                div.appendChild(options.template);

                options.template = module.exports.template.replace('<content></content>', div.innerHTML);
            }

        },

        ready: function () {

            var vm = this;

            this.$appendTo('body');

            this.modal = UIkit.modal(this.$el, {modal: false});

            this.modal.on('hide.uk.modal', function () {
                _.each(vm.__data, function (value, key) {
                    vm.$delete(key);
                });

                vm.opened = false;

                if (vm.closed) {
                    vm.closed();
                }
            });
        },

        methods: {

            open: function (data) {

                this.__data = data;
                _.each(data, function (value, key) {
                    this.$add(key, value);
                }, this);


                this.opened = true;
                this.modal.show();

            },

            close: function () {
                this.modal.hide();
            }

        }

    };

</script>
