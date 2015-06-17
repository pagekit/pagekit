<template>

    <div class="uk-overlay uk-overlay-hover" v-class="uk-display-block: !video.src">

        <video-view src="{{ video.src }}"></video-view>

        <div class="uk-placeholder uk-placeholder-large uk-text-center" v-if="!video.src">
            <img width="60" height="60" alt="{{ 'Placeholder Video' | trans }}" v-attr="src: $url.static('app/system/assets/images/placeholder-video.svg')">
        </div>

        <div class="uk-overlay-panel uk-overlay-background uk-overlay-fade uk-flex uk-flex-center uk-flex-middle uk-text-center pk-overlay-border">
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

            video: function() {
                var vm = this;
                return this.$parent.videos[_.findIndex(this.$parent._children, function(child) { return child === vm; })] || {};
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
