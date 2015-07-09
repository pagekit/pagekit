<template>

    <div class="uk-modal" v-el="modal">
        <div class="uk-modal-dialog uk-form uk-form-stacked">

            <div class="uk-modal-header">
                <h2>{{ 'Add Image' | trans }}</h2>
            </div>

            <div class="uk-form-row">
                <input-image src="{{@ image.src }}"></input-image>
            </div>

            <div class="uk-form-row">
                <label for="form-src" class="uk-form-label">{{ 'URL' | trans }}</label>

                <div class="uk-form-controls">
                    <input id="form-src" type="text" class="uk-width-1-1" v-model="image.src">
                </div>
            </div>
            <div class="uk-form-row">
                <label for="form-alt" class="uk-form-label">{{ 'Alt' | trans }}</label>

                <div class="uk-form-controls">
                    <input id="form-alt" type="text" class="uk-width-1-1" v-model="image.alt">
                </div>
            </div>

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
                image: {src: '', alt: ''}
            }
        },

        ready: function () {

            var vm = this;

            UIkit.modal(this.$$.modal).show().on('hide.uk.modal', function () {
                vm.$destroy(true);
            });
        },

        methods: {

            update: function () {
                this.$emit('select', this.image);
            }

        }

    });

</script>
