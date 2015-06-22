<template>

    <li id="comment-{{ comment.id }}">

        <article class="uk-comment">

            <header class="uk-comment-header">

                <img class="uk-comment-avatar" width="50" height="50" alt="{{ comment.author }}" v-gravatar="comment.email">

                <h3 class="uk-comment-title">{{ comment.author }}</h3>

                <ul class="uk-comment-meta uk-subnav uk-subnav-line">
                    <li>
                        {{ comment.created | relativeDate }}
                    </li>
                    <li>
                        <a v-attr="href: permalink">#</a>
                    </li>
                    <li v-if="!comment.status">{{ 'The comment is awaiting approval.' }}</li>
                </ul>

            </header>

            <div class="uk-comment-body">

                <p>{{ comment.content }}</p>

                <p v-if="showReplyButton"><a href="#" v-on="click: replyTo"><i class="uk-icon-reply"></i> {{ 'Reply' | trans }}</a></p>

            </div>

            <comments-reply v-if="showReply"></comments-reply>

        </article>

        <ul v-if="tree[comment.id] && depth < config.max_depth">
            <comments-item v-repeat="comment: tree[comment.id]" depth="{{ 1 + depth }}"></comments-item>
        </ul>

    </li>

    <comments-item v-repeat="comment: remainder" depth="{{ depth }}"></comments-item>

</template>

<script>

    module.exports = {

        inherit: true,
        replace: true,

        props: ['depth'],

        computed: {

            showReply: function() {

                return this.config.enabled && this.reply && this.reply == this.comment.id;

            },

            showReplyButton: function() {

                return this.config.enabled && this.depth < this.config.max_depth && !this.showReply;

            },

            remainder: function() {

                return this.depth >= this.config.max_depth && this.tree[this.comment.id] || [];

            },

            permalink: function() {

                return this.post.url + '#' + this.comment.id;

            }

        },

        methods: {

            replyTo: function(e) {
                e.preventDefault();
                this.$set('reply', this.comment.id);
            }

        }

    };

</script>
