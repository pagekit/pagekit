<template>

    <div>
        <img class="uk-width-1-1" v-attr="src: image" v-if="image">
        <video class="uk-width-1-1" v-attr="src: video" v-if="video">
    </div>

</template>

<script>

    module.exports = Vue.extend({

        props: ['src'],

        data: function() {
            return {image: undefined, video: undefined};
        },

        watch: {
            src: {
                handler: 'update',
                immediate: true
            }
        },

        methods: {

            update: function(src) {

                this.$set('image', undefined);
                this.$set('video', undefined);

                if (matches = (src.match(/(?:\/\/.*?youtube\.[a-z]+)\/watch\?v=([^&]+)&?(.*)/) || src.match(/youtu\.be\/(.*)/))) {

                    this.image = '//img.youtube.com/vi/' + matches[1] + '/hqdefault.jpg';

                } else if (src.match(/(\/\/.*?)vimeo\.[a-z]+\/([0-9]+).*?/)) {

                    var id = btoa(src), session = sessionStorage || {};

                    if (session[id]) {

                        this.image = session[id];

                    } else {

                        this.$http.jsonp('http://vimeo.com/api/oembed.json', { url: src }, function (data) {

                            session[id] = data.thumbnail_url;
                            this.image = session[id];

                        });

                    }

                } else {

                    this.video = this.$url(src);

                }

            }

        }

    });

</script>
