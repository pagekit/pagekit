<template>

    <div>

        <div class="uk-placeholder uk-text-center" v-if="!imageurl">

            <img v-attr="src: (url+'/app/system/modules/editor/assets/images/placeholder-image.svg')" width="60" height="60" alt="{{ 'Placeholder Image' | trans }}">
            <p>
                <a v-on="click: pick()">{{ 'Select an image' | trans }}</a>
            </p>
        </div>

        <figure class="uk-overlay uk-overlay-hover" v-if="imageurl">
            <img v-attr="src: resolveUrl(imageurl)">
            <figcaption class="uk-overlay-panel uk-overlay-background">

                <ul class="uk-subnav">
                    <li><a v-on="click: (imageurl = '')"><i class="uk-icon-trash-o"></i></a></li>
                    <li><a v-on="click: pick()"><i class="uk-icon-edit"></i></a></li>
                </ul>

            </figcaption>
        </figure>

        <div class="uk-modal" v-el="finder">

            <div class="uk-modal-dialog uk-modal-dialog-large">

                <h1 class="uk-h3">{{ 'Select Image' | trans }}</h1>
                <v-finder root="/storage"></v-finder>
                <div class="uk-margin-top">
                    <button class="uk-button uk-button-primary" type="button" v-attr="disabled: !finder.select" v-on="click: select(finder.select)">{{ 'Select' | trans }}</button>
                    <button class="uk-button uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
                </div>

            </div>
        </div>

    </div>

</template>


<script>

    var $ = require('jquery');
    var Vue = require('vue');

    module.exports = {

        replace: true,

        template: __vue_template__,

        paramAttributes: ['src'],

        data: function () {
            return $.extend({
                imageurl: ''
            }, $pagekit);
        },

        ready: function () {

            var vm = this;

            this.finder = UIkit.modal(this.$$.finder);

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
                this.finder.show();
            },

            select: function(url) {

                this.imageurl = this.finder.select;

                if (this.src) {
                    this.$parent.$set(this.src, this.imageurl);
                }

                this.finder.hide();
            },

            resolveUrl: function(url) {



                return this.$url.static(url);
            }
        }
    };

    Vue.component('v-imagepicker', module.exports);

</script>
