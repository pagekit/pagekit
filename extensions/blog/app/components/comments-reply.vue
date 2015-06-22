<template>

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

</template>

<script>

    module.exports = {

        inherit: true,

        data: function() {
            return {
                author: '',
                email: '',
                content: ''
            };
        },

        methods: {

            cancel: function (e) {
                e.preventDefault();
                this.$set('reply', 0);
                this.$set('author', '');
                this.$set('email', '');
                this.$set('content', '');
                this.$set('replyForm', {});
            },

            save: function (e) {
                e.preventDefault();

                var comment = {
                    parent_id: this.comment ? this.comment.id : 0,
                    post_id: this.config.post,
                    content: this.content
                };

                if (!this.user.isAuthenticated) {
                    comment['author'] = this.author;
                    comment['email'] = this.email;
                }

                // TODO handle errors
                this.$resource('api/blog/comment/:id').save({ id: 0 }, { comment: comment }, function (data) {
                    this.cancel(e);
                    this.load();

                    UIkit.notify(this.$trans('Thanks for commenting.'));
                });
            }

        },

        validators: {

            name: function(value) {
                return !this.config.requireinfo || Vue.validators['required'](value);
            },

            email: function(value) {
                return !this.config.requireinfo && !Vue.validators['required'](value) || Vue.validators['email'](value);
            }

        }

    };

</script>
