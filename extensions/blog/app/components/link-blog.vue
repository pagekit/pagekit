<template>

    <div class="uk-form-row">
        <label for="form-link-blog" class="uk-form-label">{{ 'View' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-link-blog" class="uk-width-1-1" v-model="link" options="postOptions"></select>
        </div>
    </div>

</template>

<script>

    module.exports = {

        link: {
            label: 'Blog'
        },

        props: ['link'],

        data: function () {
            return {
                posts: []
            }
        },

        created: function () {
            this.$resource('api/blog/post').get({limit: 100}, function (data) {
                this.$set('posts', data.posts);
            });
        },

        ready: function() {
            this.link = '@blog';
        },

        computed: {

            postOptions: function () {
                return [{text: this.$trans('Posts View'), value: '@blog'}].concat({label: this.$trans('Posts'), options: _.map(this.posts, function (post) {
                    return {text: post.title, value: '@blog/id?id='+post.id};
                })});
            }

        }

    };

    window.Links.components['link-blog'] = module.exports;

</script>
