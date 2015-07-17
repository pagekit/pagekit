<template>

    <div class="uk-modal" v-el="modal">
        <div class="uk-modal-dialog uk-form uk-form-stacked" v-class="uk-modal-dialog-large: view == 'finder'">

            <div v-show="view == 'settings'">

                <div class="uk-modal-header">
                    <h2>{{ 'Add Video' | trans }}</h2>
                </div>

                <a class="uk-placeholder uk-text-center uk-display-block" v-on="click: openFinder" v-if="!video.src">
                    <img width="60" height="60" alt="{{ 'Placeholder Image' | trans }}" v-attr="src: $url.static('app/system/assets/images/placeholder-video.svg')">
                    <p class="uk-text-muted uk-margin-small-top">{{ 'Select video' | trans }}</p>
                </a>

                <div class="uk-panel uk-overlay-hover uk-flex uk-flex-center uk-flex-middle uk-margin" v-if="video.src">
                    <div class="uk-overlay">
                        <video-view src="{{ video.src }}"></video-view>
                        <div class="uk-overlay-panel uk-overlay-background uk-overlay-fade"></div>
                    </div>
                    <a class="uk-position-cover" v-on="click: openFinder"></a>
                </div>

                <div class="uk-form-row">
                    <label for="form-src" class="uk-form-label">{{ 'URL' | trans }}</label>
                    <div class="uk-form-controls">
                        <input class="uk-width-1-1" type="text" placeholder="{{ 'URL' | trans }}" v-model="video.src" lazy>
                    </div>
                </div>

                <div class="uk-modal-footer uk-text-right">
                    <button class="uk-button uk-button-link uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
                    <button class="uk-button uk-button-link uk-modal-close" type="button" v-on="click: update">{{ 'Update' | trans }}</button>
                </div>

            </div>

            <div v-if="view == 'finder'">

                <panel-finder root="{{ storage }}" v-ref="finder" modal="true"></panel-finder>

                <div class="uk-modal-footer uk-text-right">
                    <button class="uk-button uk-button-link" type="button" v-on="click: cancel">{{ 'Cancel' | trans }}</button>
                    <button class="uk-button uk-button-link" type="button" v-attr="disabled: !selected" v-on="click: select">{{ 'Select' | trans }}</button>
                </div>

            </div>

        </div>
    </div>

</template>

<script>

    module.exports = Vue.extend({

        data: function() {
            return {
                view: 'settings',
                video: { src: '' },
                storage: window.$pagekit.storage ? window.$pagekit.storage : '/storage'
            };
        },

        ready: function () {

            var vm = this;

            UIkit.modal(this.$$.modal).show().on('hide.uk.modal', function () {
                vm.$destroy(true);
            });

        },

        methods: {

            update: function () {
                this.$emit('select', this.video);
            },

            openFinder: function () {
                this.view = 'finder';
            },

            select: function(e) {
                e.preventDefault();
                this.video.src = this.$.finder.getSelected()[0];
                this.cancel(e);
            },

            cancel: function(e) {
                e.preventDefault();
                this.view = 'settings';
            }

        },

        computed: {

            selected: function() {
                var selected = this.$.finder.getSelected();
                return selected.length == 1 && selected[0].match(/\.(mpeg|ogv|mp4|webm|wmv)$/i);
            }

        }

    });

</script>
