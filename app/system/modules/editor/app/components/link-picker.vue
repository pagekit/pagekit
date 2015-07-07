<template>

    <div class="uk-modal" v-el="modal">
        <div class="uk-modal-dialog uk-form uk-form-stacked">

            <div class="uk-modal-header">
                <h2>{{ 'Add Link' | trans }}</h2>
            </div>

            <div class="uk-form-row">
                <label for="form-link-title" class="uk-form-label">{{ 'Title' | trans }}</label>

                <div class="uk-form-controls">
                    <input id="form-link-title" type="text" class="uk-form-width-large" v-model="link.txt">
                </div>
            </div>

            <panel-link url="{{@ link.link }}"></panel-link>

            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-link uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
                <button class="uk-button uk-button-link uk-modal-close" type="button" v-on="click: update">{{ 'Update' | trans }}</button>
            </div>

        </div>
    </div>

</template>

<script>

    module.exports = Vue.extend({

        data: function () {
            return {
                view: 'settings',
                style: '',
                link: {link: '', txt: '', class: ''}
            }
        },

        ready: function () {

            var vm = this, modal = UIkit.modal(this.$$.modal);

            modal.on('hide.uk.modal', function () {
                vm.$destroy(true);
            });

            modal.show();

        },


        methods: {

            update: function () {
                this.$emit('select', this.link);
            }

        }

    });

</script>
