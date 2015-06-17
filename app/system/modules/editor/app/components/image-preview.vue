<template>

    <div class="uk-overlay uk-overlay-hover" v-class="uk-display-block: !image.src">

        <img v-attr="src: $url(image.src), alt: image.alt" v-if="image.src">

        <div class="uk-placeholder uk-placeholder-large uk-text-center" v-if="!image.src" >
            <img width="60" height="60" alt="{{ 'Placeholder Image' | trans }}" v-attr="src: $url.static('app/system/assets/images/placeholder-image.svg')">
        </div>

        <div class="uk-overlay-panel uk-overlay-background uk-overlay-fade uk-flex uk-flex-center uk-flex-middle pk-overlay-border">
            <div>
                <ul class="uk-subnav pk-subnav-icon">
                    <li><a class="pk-icon-edit pk-icon-hover" title="{{ 'Edit' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: config()"></a></li>
                    <li><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: remove()"></a></li>
                </ul>
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
