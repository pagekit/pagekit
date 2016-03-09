<template>

    <div class="uk-panel uk-placeholder uk-placeholder-large uk-text-center uk-visible-hover" v-if="!video.data.src">

        <img width="60" height="60" :alt="'Placeholder Video' | trans" :src="$url('app/system/assets/images/placeholder-video.svg')">
        <p class="uk-text-muted uk-margin-small-top">{{ 'Add Video' | trans }}</p>

        <a class="uk-position-cover" @click.prevent="config"></a>

        <div class="uk-panel-badge pk-panel-badge uk-hidden">
            <ul class="uk-subnav pk-subnav-icon">
                <li><a class="pk-icon-delete pk-icon-hover" :title="'Delete' | trans" data-uk-tooltip="{delay: 500}" @click.prevent="remove"></a></li>
            </ul>
        </div>

    </div>

    <div class="uk-overlay uk-overlay-hover uk-visible-hover" v-else>

        <img :src="imageSrc" v-if="imageSrc">
        <video class="uk-responsive-width" :src="videoSrc" :width="width" :height="height" v-if="videoSrc"></video>

        <div class="uk-overlay-panel uk-overlay-background uk-overlay-fade"></div>

        <a class="uk-position-cover" @click.prevent="config"></a>

        <div class="uk-panel-badge pk-panel-badge uk-hidden">
            <ul class="uk-subnav pk-subnav-icon">
                <li><a class="pk-icon-delete pk-icon-hover" :title="'Delete' | trans" data-uk-tooltip="{delay: 500}" @click.prevent="remove"></a></li>
            </ul>
        </div>

    </div>

</template>

<script>

    module.exports = {

        props: ['index'],

        data: function() {
            return {imageSrc: false, videoSrc: false, width: '', height: ''};
        },

        watch: {
            'video.data': {
                handler: 'update',
                immediate: true,
                deep: true
            }
        },

        computed: {

            video: function() {
                return this.$parent.videos[this.index] || {};
            }

        },

        methods: {

            config: function() {
                this.$parent.openModal(this.video);
            },

            remove: function() {
                this.video.replace('');
            },

            update: function (data) {

                var matches;

                this.$set('imageSrc', false);
                this.$set('videoSrc', false);
                this.$set('width', data.width || 690);
                this.$set('height', data.height || 390);

                var src = data.src || '';
                if (matches = (src.match(/.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/))) {

                    this.imageSrc = '//img.youtube.com/vi/' + matches[1] + '/hqdefault.jpg';

                } else if (src.match(/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/)) {

                    this.$http.get('http://vimeo.com/api/oembed.json', {url: src}, {cache: 10}).then(function (res) {
                        this.imageSrc = res.data.thumbnail_url;
                    });

                } else {

                    this.videoSrc = this.$url(src);

                }

            }

        }

    };

</script>
