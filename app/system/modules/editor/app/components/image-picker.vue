<template>

    <div class="uk-modal" v-el="modal">
        <div class="uk-modal-dialog uk-form uk-form-stacked" v-class="uk-modal-dialog-large: view == 'finder'">

            <div v-show="view == 'settings'">
                <h1 class="uk-h3">{{ 'Image' | trans }}</h1>

                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <div class="pk-thumbnail pk-thumbnail-image" v-attr="style: style"></div>
                        <p class="uk-margin-small-top"><a v-on="click: openFinder">{{ 'Select image' | trans }}</a></p>
                    </div>
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
                <div class="uk-form-row uk-margin-top">
                    <button class="uk-button uk-button-primary uk-modal-close" type="button" v-on="click: update">{{ 'Update' | trans }}</button>
                    <button class="uk-button uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
                </div>
            </div>

            <div v-if="view == 'finder'">
                <h1 class="uk-h3">{{ 'Select Image' | trans }}</h1>
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

        data: function () {
            return {
                view: 'settings',
                style: '',
                image: { src: '', alt: '' },
                finder: { root: '', select: '' }
            }
        },

        ready: function () {

            var vm = this, modal = UIkit.modal(this.$$.modal);

            modal.on('hide.uk.modal', function () {
                vm.$destroy(true);
            });

            modal.show();

            this.$on('select.finder', function (selected) {
                this.finder.select = selected.length == 1 && selected[0].match(/\.(png|jpg|jpeg|gif|svg)$/i) ? selected[0] : '';
            });

            this.$watch('image.src', this.preview);
            this.preview();
        },

        methods: {

            update: function () {
                this.$emit('select', this.image);
            },

            preview: function () {

                var vm = this, img = new Image(), src = '';

                if (this.image.src) {
                    src = this.$url.static(this.image.src);
                }

                img.onerror = function () {
                    vm.style = '';
                };

                img.onload = function () {
                    vm.style = 'background-image: url("' + src + '"); background-size: contain';
                };

                img.src = src;
            },

            openFinder: function () {
                this.view = 'finder';
                this.finder.select = '';
            },

            closeFinder: function (select) {
                this.view = 'settings';
                if (select) this.image.src = select;
            }

        }

    });

</script>
