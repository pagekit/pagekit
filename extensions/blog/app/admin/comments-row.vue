<template>

    <tr class="check-item" v-if="!editComment" v-class="uk-active: active(comment)">

        <td class="pk-blog-comments-padding"><input type="checkbox" name="id" value="{{ comment.id }}"></td>
        <td class="pk-table-width-minimum">
            <img class="uk-img-preserve uk-border-circle" width="40" height="40" alt="{{ comment.author }}" v-gravatar="comment.email">
        </td>
        <td class="uk-visible-hover">

            <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
                <div>
                    <a v-attr="href: $url('admin/user/edit', { id: comment.user_id })" v-if="comment.user_id">{{ comment.author }}</a>
                    <span v-if="!comment.user_id">{{ comment.author }}</span>
                    <br><a class="uk-link-muted" href="mailto:{{ comment.email }}">{{ comment.email }}</a>
                </div>
                <div class="uk-flex uk-flex-middle">
                    <ul class="uk-subnav pk-subnav-icon uk-invisible uk-margin-right">
                        <li><a class="pk-icon-edit pk-icon-hover" title="{{ 'Edit' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: edit"></a></li>
                        <li><a class="pk-icon-edit pk-icon-hover" title="{{ 'Reply' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: reply"></a></li>
                    </ul>

                    <a class="uk-link-muted" v-if="comment.post.isAccessible" v-attr="href: comment.post.url+'#comment-'+comment.id">{{ comment.created | relativeDate }}</a>
                    <span v-if="!comment.post.isAccessible">{{ comment.created | relativeDate }}</span>
                </div>
            </div>

            <div>{{ comment.content }}</div>

            <div class="uk-margin-top" v-if="replyComment">
                <form name="replyform" class="uk-form" v-on="valid: submit">

                    <div class="uk-form-row">
                        <textarea class="uk-width-1-1" name="content" rows="10" v-model="replyComment.content" v-valid="required"></textarea>
                        <p class="uk-form-help-block uk-text-danger" v-show="replyform.content.invalid">{{ 'Content cannot be blank.' | trans }}</p>
                    </div>

                    <p>
                        <button class="uk-button uk-button-primary" type="submit">{{ 'Reply' | trans }}</button>
                        <button class="uk-button" v-on="click: cancel">{{ 'Cancel' | trans }}</button>
                    </p>

                </form>
            </div>

        </td>
        <td class="pk-blog-comments-padding uk-text-center">
            <a href="#" title="{{ getStatusText(comment) }}" v-class="
                pk-icon-circle-success: comment.status == 1,
                pk-icon-circle-warning: comment.status == 0,
                pk-icon-circle-danger:  comment.status == 2
            " v-on="click: toggleStatus(comment)">
            </a>
        </td>
        <td class="pk-blog-comments-padding">
            <a v-attr="href: $url('admin/blog/post/edit', { id: comment.post.id })">{{ comment.post.title }}</a>
            <p>
                <a class="uk-text-nowrap" v-class="pk-link-icon: !comment.post.comments_pending" v-attr="href: $url('admin/blog/comment', { post: comment.post.id })" title="{{ '{0} No pending|{1} One pending|]1,Inf[ %comments_pending% pending' | transChoice comment.post.comments_pending comment.post }}"><i class="pk-icon-comment" v-class="pk-icon-primary: comment.post.comments_pending"></i> {{ comment.post.comment_count }}</a>
            </p>
        </td>

    </tr>

    <tr v-if="editComment">

        <td></td>
        <td class="pk-table-width-minimum">
            <img class="uk-img-preserve uk-border-circle" width="40" height="40" alt="{{ editComment.author }}" v-gravatar="editComment.email">
        </td>
        <td colspan="3">
            <form class="uk-form uk-form-stacked" name="editform" v-on="valid: submit">

                <div class="uk-grid uk-grid-medium uk-grid-width-medium-1-3" data-uk-margin="{cls:'uk-margin-top'}">
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
                        <select id="form-status" class="uk-width-1-1" v-model="editComment.status" options="statuses | toOptions"></select>
                    </div>
                </div>

                <div class="uk-grid uk-grid-medium uk-grid-width-1-1">
                    <div>
                        <textarea class="uk-width-1-1" name="content" rows="10" v-model="editComment.content" v-valid="required"></textarea>
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
