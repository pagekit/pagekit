<template>

    <div class="uk-modal" v-el="modal">
        <div class="uk-modal-dialog uk-modal-dialog-large uk-form" v-class="uk-modal-dialog-large: view == 'finder'">

            <div v-show="view == 'settings'">
                <h1 class="uk-h3">{{ 'Video' | trans }}</h1>

                <div class="uk-grid">
                    <div class="uk-width-1-3 uk-text-center">
                        <div v-if="video.src">{{{ $parent.preview(video.src) }}}</div>
                    </div>

                    <div class="uk-width-2-3">

                        <div class="uk-form-row">
                            <input type="text" class="uk-width-4-5" placeholder="{{ 'URL' | trans }}" v-model="video.src">
                            <button type="button" class="uk-button uk-float-right uk-width-1-6" v-on="click: openFinder">{{ 'Select video' | trans }}</button>
                        </div>

                    </div>
                </div>
                <div class="uk-form-row uk-margin-top">
                    <button class="uk-button uk-button-primary uk-modal-close" type="button" v-on="click: update">{{ 'Update' | trans }}</button>
                    <button class="uk-button uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
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
