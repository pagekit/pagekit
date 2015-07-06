<template>

    <div class="uk-panel uk-placeholder uk-placeholder-large uk-text-center uk-visible-hover" v-if="!video.src">
    
        <img width="60" height="60" alt="{{ 'Placeholder Video' | trans }}" v-attr="src: $url.static('app/system/assets/images/placeholder-video.svg')">
        <p class="uk-text-muted uk-margin-small-top">{{ 'Add video' | trans }}</p>

        <a class="uk-position-cover" v-on="click: config()"></a>

        <div class="uk-panel-badge pk-panel-badge uk-hidden">
            <ul class="uk-subnav pk-subnav-icon">
                <li><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: remove()"></a></li>
            </ul>
        </div>

    </div>

    <div class="uk-panel uk-visible-hover uk-overlay-hover uk-display-inline-block" v-if="video.src">

        <div class="uk-overlay">
            <video-view src="{{ video.src }}"></video-view>
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

            video: function() {
                return this.$parent.videos[_.findIndex(this.$parent.$children, function(child) { return child === this; }, this)] || {};
            }

        },

        methods: {

            config: function() {
                this.$parent.openModal(this.video);
            },

            remove: function() {
                this.video.replace('');
            }

        }

    });

</script>
