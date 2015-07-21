<?php
$view->script('comments', 'blog:app/bundle/comments.js', ['vue', 'uikit-notify'])
?>

<div id="comments" class="uk-margin" v-if="config.enabled || comments.length">

    <div class="uk-margin" v-if="user.canView">

        <div v-show="comments.length">
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

<script id="comments-item" type="text/template">

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

</script>

<script id="comments-reply" type="text/template">

    <div class="uk-margin">

        <h3>{{ 'Leave a comment' | trans }}</h3>

        <form class="uk-form uk-form-stacked" name="replyForm" v-if="user.canComment" v-on="valid: save">

            <p v-if="user.isAuthenticated">{{ 'Logged in as %name%' | trans {name:user.name} }}</p>

            <div v-if="!user.isAuthenticated" class="uk-form-row">

                <p>{{ 'Your email address will not be published.' | trans }}</p>

                <div class="uk-form-row">
                    <label for="form-name" class="uk-form-label">{{ 'Name' | trans }}</label>

                    <div class="uk-form-controls">
                        <input id="form-name" class="uk-form-width-large" type="text" name="author" v-model="author" v-valid="name">
                        <p class="uk-form-help-block uk-text-danger" v-show="replyForm.author.invalid">{{ 'Name cannot be blank.' | trans }}</p>
                    </div>
                </div>
                <div class="uk-form-row">
                    <label for="form-email" class="uk-form-label">{{ 'Email' | trans }}</label>

                    <div class="uk-form-controls">
                        <input id="form-email" class="uk-form-width-large" type="email" name="email" v-model="email" v-valid="email">
                        <p class="uk-form-help-block uk-text-danger" v-show="replyForm.email.invalid">{{ 'Email invalid.' | trans }}</p>
                    </div>
                </div>

            </div>

            <div class="uk-form-row">
                <label for="form-comment" class="uk-form-label">{{ 'Comment' | trans }}</label>

                <div class="uk-form-controls">
                    <textarea id="form-comment" class="uk-form-width-large" name="content" rows="10" v-model="content" v-valid="required"></textarea>
                    <p class="uk-form-help-block uk-text-danger" v-show="replyForm.content.invalid">{{ 'Comment cannot be blank.' | trans }}</p>
                </div>
            </div>

            <p>
                <input class="uk-button uk-button-primary" type="submit" value="{{ 'Submit comment' | trans }}" accesskey="s">
                <a href="#" v-on="click: cancel">{{ 'Cancel' | trans }}</a>
            </p>

        </form>

        <p v-if="user.isAuthenticated && !user.canComment">{{ 'You are not allowed to post comments.' | trans }}</p>

        <p v-if="!user.isAuthenticated && !user.canComment">{{ 'Please login to leave a comment.' | trans }}</p>

    </div>

</script>
