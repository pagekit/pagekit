<template>

    <div class="uk-overlay uk-overlay-hover uk-display-block">

        <img v-attr="src: $url(image.src), alt: image.alt" v-if="image.src">

        <div class="uk-placeholder uk-placeholder-large uk-text-center uk-vertical-align" v-if="!image.src" >
            <div class="uk-vertical-align-middle"><img v-attr="src: $url.static('app/system/assets/images/placeholder-editor-image.svg')" width="60" height="60" alt="{{ 'Placeholder Image' | trans }}"></div>
        </div>

        <div class="uk-overlay-panel uk-overlay-background uk-overlay-fade uk-flex uk-flex-center uk-flex-middle uk-text-center pk-overlay-border">
            <div>
                <h3 class="uk-margin-small-bottom">{{ 'Image' | trans }}</h3>
                <div data-uk-margin>
                    <button class="uk-button uk-button-primary" type="button" v-on="click: config()">{{ 'Settings' | trans }}</button>
                    <button class="uk-button uk-button-danger" type="button" v-on="click: remove()">{{ 'Delete' | trans }}</button>
                </div>
            </div>
        </div>

    </div>

</template>

<script>

    module.exports = Vue.extend({

        computed: {

            image: function() {
                var vm = this;
                return this.$parent.images[_.findIndex(this.$parent._children, function(child) { return child === vm; })] || {};
            }

        },

        methods: {

            config: function() {
                this.$parent.openModal(this.image);
            },

            remove: function() {
                this.image.replace('');
            }

        }

    });

</script>
