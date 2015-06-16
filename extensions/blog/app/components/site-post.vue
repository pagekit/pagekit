<template>

    <div class="uk-form-row" v-partial="settings"></div>

    <div class="uk-form-row">

        <label for="form-post" class="uk-form-label">{{ 'Post' | trans }}</label>
        <div class="uk-form-controls">
            <select class="uk-form-width-large" v-model="node.data.variables.id" v-valid="required" options="postsOptions"></select>
        </div>

    </div>

</template>

<script>

    module.exports = {

        props: ['node', 'form', 'type'],

        section: {
            name: 'blog-post',
            label: 'Settings',
            priority: 0,
            active: 'blog-post'
        },

        created: function() {

            this.$resource('api/blog/post/:id').query(function(data) {
                this.$set('posts', data.posts);
            });

        },

        computed: {

            postsOptions: function () {
                return _.map(this.$data.posts, function (post) {
                    return { text: post.title, value: post.id };
                });
            }

        }

    };

</script>
