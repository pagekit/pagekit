<template>

    <div>

        <a class="uk-placeholder uk-text-center uk-display-block uk-margin-remove" v-if="!imageurl" v-on="click: pick()">
            <img width="60" height="60" alt="{{ 'Placeholder Image' | trans }}" v-attr="src: $url.static('app/system/assets/images/placeholder-image.svg')">
            <p class="uk-text-muted uk-margin-small-top">{{ 'Add post image' | trans }}</p>
        </a>

        <div class="uk-overlay uk-overlay-hover pk-image-max-height uk-flex uk-flex-center uk-flex-middle" v-if="imageurl">

            <img v-attr="src: resolveUrl(imageurl)">

            <div class="uk-overlay-panel uk-overlay-background uk-overlay-fade uk-flex uk-flex-center uk-flex-middle uk-text-center pk-overlay-border">
                <div>
                    <ul class="uk-subnav pk-subnav-icon">
                        <li><a class="pk-icon-edit pk-icon-hover" title="{{ 'Edit' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: pick()"></a></li>
                        <li><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: (imageurl = '')" v-confirm="'Remove Image?'"></a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div class="uk-modal" v-el="modal">
            <div class="uk-modal-dialog uk-modal-dialog-large">

                <v-finder root="{{ storage }}" v-el="finder"></v-finder>

                <div class="uk-modal-footer uk-text-right">
                    <button class="uk-button uk-button-link uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
                    <button class="uk-button uk-button-link" type="button" v-attr="disabled: !finder.select" v-on="click: select(finder.select)">{{ 'Select' | trans }}</button>
                </div>

            </div>
        </div>

    </div>

</template>


<script>

    module.exports = {

        replace: true,

        props: ['src'],

        data: function () {
            return $.extend({
                imageurl: '',
                finder: {select:''}
            }, $pagekit);
        },

        ready: function () {

            var vm = this;

            this.modal = UIkit.modal(this.$$.modal);

            this.modal.element.appendTo('body');

            this.$finder = this.$$.finder.$finder;

            this.$on('select.finder', function(selected) {

                if (selected.length == 1 && selected[0].match(/\.(png|jpg|jpeg|gif|svg)$/i)) {
                    vm.finder.select = selected[0];
                } else {
                    vm.finder.select = '';
                }
            });

            if (this.src) {
                this.imageurl = this.$parent.$get(this.src);
            }
        },

        methods: {

            pick: function() {
                this.$finder.removeSelection();
                this.modal.show();
            },

            select: function(url) {

                this.imageurl = this.finder.select;

                if (this.src) {
                    this.$parent.$set(this.src, this.imageurl);
                }

                this.modal.hide();
            },

            resolveUrl: function(url) {
                return this.$url.static(url);
            }
        },

        template: __vue_template__

    };

    Vue.component('v-imagepicker', module.exports);

</script>
