<template>

    <div class="uk-margin" v-if="config.enabled || comments.length">

        <div class="uk-margin" v-if="user.canView">

            <div v-if="comments.length">
                <h2 class="uk-h3">{{ 'Comments (%count%)' | trans {count:count} }}</h2>
                <ul class="uk-comment-list">

                    <comments-item v-repeat="comment: tree[0]" depth="0"></comments-item>

                </ul>
            </div>

            <comments-reply v-if="config.enabled && !reply"></comments-reply>

        </div>

        <p v-if="!config.enabled">{{ 'Comments are closed.' | trans }}</p>
        <p v-if="!user.canView && user.isAuthenticated">{{ 'You are not allowed to view comments.' | trans }}</p>
        <p v-if="!user.canView && !user.isAuthenticated">{{ 'Please login to view comments.' | trans }}</p>

    </div>

</template>

<script>

    module.exports = {

        data: function() {
            return _.extend({
                post: {},
                tree: {},
                comments: [],
                count: 0,
                reply: 0
            }, window.$comments);
        },

        created: function () {
            this.Comments = this.$resource('api/blog/comment/:id');
            this.load();
        },

        methods: {

            load: function () {

                if (!this.user.canView) {
                    return;
                }

                this.Comments.query({ post: this.config.post }, function (data) {
                    this.$set('comments', data.comments);
                    this.$set('tree', _.groupBy(data.comments, 'parent_id'));
                    this.$set('post', data.posts[0]);
                    this.$set('count', data.count);
                    this.$set('reply', 0);
                });
            }

        },

        components: {

            'comments-item': require('./comments-item.vue'),
            'comments-reply': require('./comments-reply.vue')

        }

    };

    jQuery(function() {
        new Vue(module.exports).$mount('#comments');
    });

</script>
