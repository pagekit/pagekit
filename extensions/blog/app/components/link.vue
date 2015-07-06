<template>

    <div class="uk-form-row">
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-form-controls-condensed">
                <label>
                    <input type="radio" value="posts" v-model="view"> {{ 'Posts View' | trans }}
                </label>
            </p>
        </div>
    </div>

    <div class="uk-form-row">
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-form-controls-condensed">
                <label>
                    <input type="radio" value="post" v-model="view"> {{ 'Post' | trans }}
                </label>
                <select class="uk-form-width-large" v-model="post" options="postOptions"></select>
            </p>
        </div>
    </div>

</template>

<script>

    module.exports = {

        link: {
            name: 'blog',
            label: 'Blog'
        },

        props: ['url'],

        data: function () {
            return {
                view: 'post',
                post: '',
                posts: []
            }
        },

        created: function () {
            this.$resource('api/blog/post').get({limit: 100}, function (data) {
                this.$set('posts', data.posts);
            });
        },

        watch: {

            url: {
                handler: function (url) {
                    var matches = (url || '').match(/^@blog\/id\?id=(\d+).*/);
                    this.post = matches ? matches[1] : '';

                },
                immediate: true
            },

            post: function (post) {
                this.url = '@blog/id?id=' + post;
                this.view = 'post';
            },

            view: function(view) {
                if (view === 'posts') {
                    this.url = '@blog';
                } else if (this.post) {
                    this.url = '@blog/id?id=' + this.post;
                }
            }

        },

        computed: {

            postOptions: function () {
                return [{text: this.$trans('- Select Post -'), value: ''}].concat(_.map(this.posts, function (post) {
                    return {text: post.title, value: post.id};
                }));
            }

        },

        template: __vue_template__

    };

    window.Linkpicker.component('blog', module.exports);

</script>
