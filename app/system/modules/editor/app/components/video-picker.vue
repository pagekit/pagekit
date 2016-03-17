<template>

    <div>
        <v-modal v-ref:modal :closed="close">
            <form class="uk-form uk-form-stacked" @submit.prevent="update">

                <div class="uk-modal-header">
                    <h2>{{ 'Add Video' | trans }}</h2>
                </div>

                <div class="uk-form-row">
                    <input-video :source.sync="video.data.src"></input-video>
                </div>

                <div class="uk-form-row">
                    <label for="form-src" class="uk-form-label">{{ 'URL' | trans }}</label>
                    <div class="uk-form-controls">
                        <input id="form-src" class="uk-width-1-1" type="text" v-model="video.data.src" debounce="500">
                    </div>
                </div>

                <div class="uk-grid uk-grid-width-1-2 uk-form-row">
                    <div>
                        <label for="form-src" class="uk-form-label">{{ 'Width' | trans }}</label>
                        <input class="uk-width-1-1" id="form-width" type="text" :placeholder="'auto' | trans" v-model="video.data.width">
                    </div>
                    <div>
                        <label for="form-src" class="uk-form-label">{{ 'Height' | trans }}</label>
                        <input class="uk-width-1-1" id="form-height" type="text" :disabled="!isVimeo && !isYoutube" :placeholder="'auto' | trans"v-model="video.data.height">
                    </div>
                </div>

                <div class="uk-form-row">
                    <label><input type="checkbox" v-model="video.data.autoplay"> {{ 'Autoplay' | trans }}</label>
                    <label class="uk-margin-small-left" v-show="!isVimeo"><input type="checkbox" v-model="video.data.controls"> {{ 'Controls' | trans }}</label>
                    <label class="uk-margin-small-left"><input type="checkbox" v-model="video.data.loop"> {{ 'Loop' | trans }}</label>
                    <label class="uk-margin-small-left" v-show="!isVimeo && !isYoutube"><input type="checkbox" v-model="video.data.muted"> {{ 'Muted' | trans }}</label>
                </div>

                <div class="uk-form-row" v-show="!isYoutube && !isVimeo">
                    <span class="uk-form-label">{{ 'Poster Image' | trans }}</span>
                    <div class="uk-form-controls">
                        <input-image class="uk-width-1-1" :source.sync="video.data.poster"></input-image>
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
                video: {data: {src: '', controls: true}}
            }
        },

        ready: function () {
            this.$refs.modal.open();
        },

        computed: {

            isYoutube: function () {
                return this.video.data.src ? this.video.data.src.match(/.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/) : false;
            },

            isVimeo: function () {
                return this.video.data.src ? this.video.data.src.match(/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/) : false;
            }

        },

        methods: {

            close: function() {
                this.$destroy(true);
            },

            update: function () {
                this.$refs.modal.close();
                this.$emit('select', this.video);
            }

        }

    };

</script>
