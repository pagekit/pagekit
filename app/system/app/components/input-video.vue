<template>

    <a class="uk-placeholder uk-text-center uk-display-block uk-margin-remove" v-if="!source" @click.prevent="pick">
        <img width="60" height="60" :alt="'Placeholder Image' | trans" :src="$url('app/system/assets/images/placeholder-video.svg')">
        <p class="uk-text-muted uk-margin-small-top">{{ 'Select Video' | trans }}</p>
    </a>

    <div class="uk-overlay uk-overlay-hover uk-visible-hover" v-else>

        <img :src="image" v-if="image">
        <video class="uk-width-1-1" :src="video" v-if="video"></video>

        <div class="uk-overlay-panel uk-overlay-background uk-overlay-fade"></div>

        <a class="uk-position-cover" @click.prevent="pick"></a>

        <div class="uk-panel-badge pk-panel-badge uk-hidden">
            <ul class="uk-subnav pk-subnav-icon">
                <li><a class="pk-icon-delete pk-icon-hover" :title="'Delete' | trans" data-uk-tooltip="{delay: 500}" @click.prevent="remove"></a></li>
            </ul>
        </div>

    </div>

    <v-modal v-ref:modal large>

        <panel-finder :root="storage" :modal="true" v-ref:finder></panel-finder>

        <div class="uk-modal-footer uk-text-right">
            <button class="uk-button uk-button-link uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
            <button class="uk-button uk-button-primary" type="button" :disabled="!selectButton" @click.prevent="select">{{ 'Select' | trans }}</button>
        </div>

    </v-modal>

</template>

<script>

    module.exports = {

        props: ['source'],

        data: function () {
            return _.merge({image: undefined, video: undefined}, $pagekit);
        },

        computed: {

            selectButton: function () {
                var selected = this.$refs.finder.getSelected();
                return selected.length === 1 && this.$refs.finder.isVideo(selected[0])
            }

        },

        watch: {
            source: {
                handler: 'update',
                immediate: true
            }
        },

        methods: {

            pick: function () {
                this.$refs.modal.open();
            },

            select: function () {
                this.source = this.$refs.finder.getSelected()[0];
                this.$refs.modal.close();
            },

            remove: function () {
                this.source = ''
            },

            update: function (src) {

                var matches;

                this.$set('image', undefined);
                this.$set('video', undefined);

                if (matches = (src.match(/(?:\/\/.*?youtube\.[a-z]+)\/watch\?v=([^&]+)&?(.*)/) || src.match(/youtu\.be\/(.*)/))) {

                    this.image = '//img.youtube.com/vi/' + matches[1] + '/hqdefault.jpg';

                } else if (src.match(/(\/\/.*?)vimeo\.[a-z]+\/([0-9]+).*?/)) {

                    var id = btoa(src);

                    if (this.$session[id]) {

                        this.image = this.$session[id];

                    } else {

                        this.$http.get('http://vimeo.com/api/oembed.json', {url: src}).then(function (res) {
                            var data = res.data;
                            this.image = this.$session[id] = data.thumbnail_url;
                        });

                    }

                } else {

                    this.video = this.$url(src);

                }

            }

        }

    };

    Vue.component('input-video', function (resolve, reject) {
        Vue.asset({
            js: [
                'app/assets/uikit/js/components/upload.min.js',
                'app/system/modules/finder/app/bundle/panel-finder.js'
            ]
        }).then(function () {
            resolve(module.exports);
        })
    });

</script>
