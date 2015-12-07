<template>

    <div>
        <v-modal v-ref:modal :closed="close">
            <form class="uk-form uk-form-stacked" @submit.prevent="update">

                <div class="uk-modal-header">
                    <h2>{{ 'Add Video' | trans }}</h2>
                </div>

                <div class="uk-form-row">
                    <input-video :source.sync="video.src"></input-video>
                </div>

                <div class="uk-form-row">
                    <label for="form-src" class="uk-form-label">{{ 'URL' | trans }}</label>
                    <div class="uk-form-controls">
                        <input class="uk-width-1-1" type="text" :placeholder="'URL' | trans" v-model="video.src" lazy>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label><input type="checkbox" v-model="video.autoplay"> {{ 'Autoplay' | trans }}</label>
                    <label v-show="!isVimeo"><input type="checkbox" v-model="video.controls"> {{ 'Controls' | trans }}</label>
                    <label><input type="checkbox" v-model="video.loop"> {{ 'Loop' | trans }}</label>
                    <label v-show="!isVimeo && !isYoutube"><input type="checkbox" v-model="video.muted"> {{ 'Muted' | trans }}</label>
                </div>

                <div class="uk-form-row" v-show="!isYoutube && !isVimeo">
                    <label for="form-src" class="uk-form-label">{{ 'Poster Image' | trans }}</label>
                    <div class="uk-form-controls">
                        <input-image class="uk-width-1-1" :source.sync="video.poster"></input-image>
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
                video: {src: '', alt: '', controls: true}
            }
        },

        ready: function () {
            this.$refs.modal.open();
        },

        computed: {

            isYoutube: function () {
                return Boolean(this.video.src.match(/(?:\/\/.*?youtube\.[a-z]+)\/watch\?v=([^&]+)&?(.*)/) || this.video.src.match(/youtu\.be\/(.*)/));
            },

            isVimeo: function () {
                return Boolean(this.video.src.match(/(\/\/.*?)vimeo\.[a-z]+\/([0-9]+).*?/));
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
