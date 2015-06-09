<template>

    <tr v-if="!editComment">

        <td>
            <input class="pk-blog-comments-margin" type="checkbox" name="id" value="{{ comment.id }}">
        </td>
        <td class="pk-table-width-minimum">
            <img class="uk-img-preserve uk-border-circle" width="40" height="40" alt="{{ comment.author }}" v-gravatar="comment.email">
        </td>
        <td>
            <div class="uk-margin uk-clearfix">
                <div class="uk-float-left uk-width-large-1-2">
                    {{ comment.author }}
                    <br>
                    <a class="uk-link-reset uk-text-muted" href="mailto:{{ comment.email }}">{{ comment.email }}</a>
                </div>
                <div class="uk-float-left uk-width-large-1-2 pk-text-right-large">
                    <a v-if="comment.post.isAccessible" v-attr="href: comment.post.url+'#comment-'+comment.id">{{ comment.created | relativeDate }}</a>
                    <!-- TODO: remove ?-->
                    <span v-if="!comment.post.isAccessible"></span>
                </div>
            </div>
            <div>{{ comment.content }}</div>
            <ul class="uk-subnav uk-subnav-line">
                <li><a v-on="click: edit">{{ 'Edit' | trans }}</a></li>
                <li><a v-on="click: reply">{{ 'Reply' | trans }}</a></li>
            </ul>
        </td>
        <td class="uk-text-center">
            <a title="{{ getStatusText(comment) }}" v-on="click: toggleStatus(comment)">
                <i class="uk-icon-circle" v-class="
                    uk-text-success: comment.status == 1,
                    uk-text-warning: comment.status == 0,
                    uk-text-danger:  comment.status == 2
                "></i>
            </a>
        </td>
        <td>
            <a v-attr="href: $url('admin/blog/post/edit', { id: comment.post.id })">{{ comment.post.title }}</a>
            <a title="{{ '{0} No pending|{1} One pending|]1,Inf[ %comments_pending% pending' | transChoice comment.post.comments_pending comment.post }}" class="uk-badge uk-badge-notification" v-class="pk-badge: comment.post.comments_pending" v-attr="href: $url('admin/blog/comment', { post: comment.post.id })">{{ comment.post.comment_count }}</a>
        </td>

    </tr>

    <tr v-if="editComment">

        <td></td>
        <td class="pk-table-width-minimum">
            <img class="uk-img-preserve uk-border-circle" width="40" height="40" alt="{{ editComment.author }}" v-gravatar="editComment.email">
        </td>
        <td colspan="3">
            <form name="editform" class="uk-form uk-form-stacked" v-on="valid: submit">

                <div class="uk-grid uk-grid-width-small-1-3" data-uk-grid-margin>
                    <div>
                        <label for="form-author" class="uk-form-label">{{ 'Name' | trans }}</label>
                        <input id="form-author" class="uk-width-1-1" name="author" type="text" v-model="editComment.author" v-valid="required">
                        <p class="uk-form-help-block uk-text-danger" v-show="editform.author.invalid">{{ 'Author cannot be blank.' | trans }}</p>
                    </div>
                    <div>
                        <label for="form-email" class="uk-form-label">{{ 'E-mail' | trans }}</label>
                        <input id="form-email" class="uk-width-1-1" name="email" type="text" v-model="editComment.email" v-valid="email" lazy>
                        <p class="uk-form-help-block uk-text-danger" v-show="editform.email.invalid">{{ 'Field must be a valid email address.' | trans }}</p>
                    </div>
                    <div>
                        <label for="form-status" class="uk-form-label">{{ 'Status' | trans }}</label>
                        <select id="form-status" class="uk-width-1-1" v-model="editComment.status" options="statuses | toOptions
                    "></select>
                    </div>
                </div>

                <div class="uk-grid uk-grid-width-1-1">
                    <div>
                        <textarea class="uk-width-1-1" name="content" rows="6" v-model="editComment.content" v-valid="required"></textarea>
                        <p class="uk-form-help-block uk-text-danger" v-show="editform.content.invalid">{{ 'Content cannot be blank.' | trans }}</p>
                    </div>
                </div>

                <p>
                    <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>
                    <button class="uk-button" v-on="click: cancel">{{ 'Cancel' | trans }}</button>
                </p>

            </form>
        </td>

    </tr>

    <tr v-if="replyComment">

        <td></td>
        <td class="pk-table-width-minimum">
            <img class="uk-img-preserve uk-border-circle" width="40" height="40" alt="{{ replyComment.author }}" v-gravatar="replyComment.email">
        </td>
        <td colspan="3">
            <form name="replyform" class="uk-form" v-on="valid: submit">

                <div class="uk-form-row">
                    <textarea class="uk-width-1-1" name="content" rows="6" v-model="replyComment.content" v-valid="required"></textarea>
                    <p class="uk-form-help-block uk-text-danger" v-show="replyform.content.invalid">{{ 'Content cannot be blank.' | trans }}</p>
                </div>

                <p>
                    <button class="uk-button uk-button-primary" type="submit">{{ 'Reply' | trans }}</button>
                    <button class="uk-button" v-on="click: cancel">{{ 'Cancel' | trans }}</button>
                </p>

            </form>
        </td>

    </tr>

</template>

<script>

    module.exports = {

        inherit: true,
        replace: true,

        events: {

            cancel: function() {
                this.$set('replyComment', undefined);
                this.$set('editComment', undefined);
            }

        },

        methods: {

            reply: function() {
                this.cancel();
                this.$set('replyComment', { parent_id: this.comment.id, author: this.user.name, email: this.user.email });
            },

            edit: function() {
                this.cancel();
                this.$set('editComment', _.merge({}, this.comment));
            },

            submit: function() {
                this.save(this.replyComment || this.editComment);
            },

            toggleStatus: function () {
                this.comment.status = this.comment.status === 1 ? 0 : 1;
                this.save(this.comment);
            }

        }

    };

</script>
