<template>

    <div class="uk-panel uk-placeholder uk-placeholder-large uk-text-center uk-visible-hover" v-if="!image.src">

        <img width="60" height="60" alt="{{ 'Placeholder Image' | trans }}" v-attr="src: $url.static('app/system/assets/images/placeholder-image.svg')">
        <p class="uk-text-muted uk-margin-small-top">{{ 'Add image' | trans }}</p>

        <a class="uk-position-cover" v-on="click: config()"></a>

        <div class="uk-panel-badge pk-panel-badge uk-hidden">
            <ul class="uk-subnav pk-subnav-icon">
                <li><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: remove()"></a></li>
            </ul>
        </div>

    </div>

    <div class="uk-panel uk-visible-hover uk-overlay-hover uk-display-inline-block" v-if="image.src">

        <div class="uk-overlay">
            <img v-attr="src: $url(image.src), alt: image.alt">
            <div class="uk-overlay-panel uk-overlay-background uk-overlay-fade"></div>
        </div>

        <a class="uk-position-cover" v-on="click: config()"></a>

        <div class="uk-panel-badge pk-panel-badge uk-hidden">
            <ul class="uk-subnav pk-subnav-icon">
                <li><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: remove()"></a></li>
            </ul>
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
