<template>

    <div class="uk-modal" v-el="modal">
        <div class="uk-modal-dialog uk-modal-dialog-large uk-form" v-class="uk-modal-dialog-large: view == 'finder'">

            <div v-show="view == 'settings'">
                <h1 class="uk-h3">{{ 'Video' | trans }}</h1>



                <a class="uk-placeholder uk-text-center uk-display-block uk-margin-remove" v-on="click: openFinder" v-if="!video.src">
                    <img width="60" height="60" alt="{{ 'Placeholder Image' | trans }}" v-attr="src: $url.static('app/system/assets/images/placeholder-video.svg')">
                    <p class="uk-text-muted uk-margin-small-top">{{ 'Select image' | trans }}</p>
                </a>

                <div class="uk-overlay uk-overlay-hover pk-image-max-height uk-flex uk-flex-center uk-flex-middle" v-if="video.src">
                    <video-view src="{{ video.src }}"></video-view>
                    <div class="uk-overlay-panel uk-overlay-background uk-overlay-fade uk-flex uk-flex-center uk-flex-middle uk-text-center">
                        <div>
                            <a class="pk-icon-edit pk-icon-hover" title="{{ 'Edit' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: openFinder"></a>
                        </div>
                    </div>
                </div>

                <div class="uk-margin">
                    <input type="text" class="uk-width-1-1" placeholder="{{ 'URL' | trans }}" v-model="video.src" lazy>
                </div>

                <div class="uk-form-row uk-margin-top uk-text-right">
                    <button class="uk-button uk-button-link uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
                    <button class="uk-button uk-button-link uk-button-primary uk-modal-close" type="button" v-on="click: update">{{ 'Update' | trans }}</button>
                </div>
            </div>

            <div v-if="view == 'finder'">
                <h1 class="uk-h3">{{ 'Select Video' | trans }}</h1>
                <v-finder root="{{ finder.root }}" v-ref="finder"></v-finder>
                <div class="uk-margin-top">
                    <button class="uk-button uk-button-primary" type="button" v-attr="disabled: !finder.select" v-on="click: closeFinder(finder.select)">{{ 'Select' | trans }}</button>
                    <button class="uk-button" type="button" v-on="click: closeFinder(false)">{{ 'Cancel' | trans }}</button>
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
                finder: { root: '', select: '' }
            };
        },

        ready: function () {

            var vm = this, modal = UIkit.modal(this.$$.modal);

            modal.on('hide.uk.modal', function () {
                vm.$destroy(true);
            });

            modal.show();

            this.$on('select.finder', function (selected) {
                this.finder.select = selected.length == 1 && selected[0].match(/\.(mpeg|ogv|mp4|webm|wmv)$/i) ? selected[0] : '';
            });

        },

        methods: {

            update: function () {
                this.$emit('select', this.video);
            },

            openFinder: function () {
                this.view = 'finder';
                this.finder.select = '';
            },

            closeFinder: function (select) {
                this.view = 'settings';
                if (select) this.video.src = select;
            }

        }

    });

</script>
