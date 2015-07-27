<template>

    <div>
        <v-modal v-ref="modal" closed="{{ close }}">
            <form class="uk-form uk-form-stacked" v-on="submit: update">

                <div class="uk-modal-header">
                    <h2>{{ 'Add Video' | trans }}</h2>
                </div>

                <div class="uk-form-row">
                    <input-video source="{{@ video.src }}"></input-video>
                </div>

                <div class="uk-form-row">
                    <label for="form-src" class="uk-form-label">{{ 'URL' | trans }}</label>
                    <div class="uk-form-controls">
                        <input class="uk-width-1-1" type="text" placeholder="{{ 'URL' | trans }}" v-model="video.src" lazy>
                    </div>
                </div>

                <div class="uk-modal-footer uk-text-right">
                    <button class="uk-button uk-button-link uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
                    <button class="uk-button uk-button-link" type="submit">{{ 'Update' | trans }}</button>
                </div>

            </form>

        </v-modal>
    </div>

</template>

<script>

    module.exports = {

        data: function () {
            return {
                video: {src: '', alt: ''}
            }
        },

        ready: function () {
            this.$.modal.open();
        },

        methods: {

            close: function() {
                this.$destroy(true);
            },

            update: function (e) {
                e.preventDefault();
                this.$.modal.close();
                this.$emit('select', this.video);
            }

        }

    };

</script>
