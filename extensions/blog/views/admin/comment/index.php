<?php $view->style('comment-index', 'blog:assets/css/blog.admin.css') ?>
<?php $view->script('comment-index', 'blog:app/admin/comment/index.js', ['system', 'vue-validator', 'gravatar']) ?>

<div id="js-comments" class="uk-form" v-cloak>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

            <h2 class="uk-margin-remove" v-show="!selected.length">{{ '{0} %count% Comments|{1} %count% Comment|]1,Inf[ %count% Comments' | transChoice count {count:count} }}</h2>
            <h2 class="uk-margin-remove" v-show="selected.length">{{ '{1} %count% Comment selected|]1,Inf[ %count% Comments selected' | transChoice selected.length {count:selected.length} }}</h2>

            <div class="uk-margin-left" v-show="selected.length">
                <ul class="uk-subnav pk-subnav-icon">
                    <li><a class="uk-icon-trash-o" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-show="selected.length" v-on="click: remove"></a></li>
                    <li><a class="uk-icon-check-circle-o" title="{{ 'Approve' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: status(1)"></a></li>
                    <li><a class="uk-icon-ban" title="{{ 'Unapprove' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: status(0)"></a></li>
                    <li><a class="uk-icon-frown-o" title="{{ 'Mark as spam' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: status(2)"></a></li>
                </ul>
            </div>

            <div class="pk-search">
                <div class="uk-search">
                    <input class="uk-search-field" type="text" v-model="config.filter.search" debounce="300">
                </div>
            </div>

        </div>
    </div>

    <div class="uk-overflow-container">
        <table class="uk-table">
            <thead>
                <tr>
                    <th class="pk-table-width-minimum"><input type="checkbox" v-check-all="selected: input[name=id]"></th>
                    <th class="pk-table-min-width-300" colspan="2">{{ 'Comment' | trans }}</th>
                    <th class="pk-table-width-100 uk-text-center">
                        <div class="uk-form-select pk-filter" data-uk-form-select>
                            <span>{{ 'Status' | trans }}</span>
                            <select v-model="config.filter.status" options="statusOptions"></select>
                        </div>
                    </th>
                    <th class="pk-table-width-200">
                        <span v-if="!config.post">{{ 'In response to' | trans }}</span>
                        <span v-if="config.post">{{ 'In response to %title%' | trans config.post }}</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-repeat="comment: comments" v-show="!editing || editing.id !== comment.id || editing === comment" v-partial="#comment.{{ !comment.id ? 'reply' : ( editing === comment ? 'edit' : 'default') }}"></tr>
            </tbody>
        </table>
    </div>

    <p v-show="comments && !comments.length" class="uk-alert uk-alert-info">{{ 'No comments found.' | trans }}</p>

    <v-pagination page="{{ config.page }}" pages="{{ pages }}" v-show="pages > 1"></v-pagination>

</div>

<script id="comment.default" type="text/template">

    <td>
        <input class="pk-blog-comments-margin" type="checkbox" name="id" value="{{ comment.id }}">
    </td>
    <td class="pk-table-width-minimum">
        <img v-gravatar="comment.email" class="uk-img-preserve uk-border-circle" width="40" height="40" alt="{{ comment.author }}">
    </td>
    <td>
        <div class="uk-margin uk-clearfix">
            <div class="uk-float-left uk-width-large-1-2">
                {{ comment.author }}
                <br>
                <a class="uk-link-reset uk-text-muted" href="mailto:{{ comment.email }}">{{ comment.email }}</a>
            </div>
            <div class="uk-float-left uk-width-large-1-2 pk-text-right-large">
                <a v-if="comment.post.isAccessible" v-attr="href: comment.post.url+'#comment-'+comment.id">{{ comment.created | date medium }}</a>
                <span v-if="!comment.post.isAccessible"></span>
            </div>
        </div>
        <div>{{ comment.content }}</div>
        <ul class="uk-subnav uk-subnav-line">
            <li><a v-on="click: edit(comment)">{{ 'Edit' | trans }}</a></li>
            <li><a v-on="click: reply(comment)">{{ 'Reply' | trans }}</a></li>
        </ul>
    </td>
    <td class="uk-text-center">
        <a v-on="click: toggleStatus(comment)" title="{{ getStatusText(comment) }}">
            <i class="uk-icon-circle" v-class="
                uk-text-success: comment.status == 1,
                uk-text-warning: comment.status == 0,
                uk-text-danger:  comment.status == 2
            "></i>
        </a>
    </td>
    <td>
        <a v-attr="href: $url('admin/blog/post/edit', { id: comment.post.id })">{{ comment.post.title }}</a>
        <a class="uk-badge uk-badge-notification" v-class="pk-badge: comment.post.comments_pending" v-attr="href: $url('admin/blog/comment', { post: comment.post.id })" title="{{ '{0} No pending|{1} One pending|]1,Inf[ %comments_pending% pending' | transChoice comment.post.comments_pending comment.post }}">{{ comment.post.comment_count }}</a>
    </td>

</script>

<script id="comment.edit" type="text/template">

    <td></td>
    <td class="pk-table-width-minimum">
        <img v-gravatar="comment.email" class="uk-img-preserve uk-border-circle" width="40" height="40" alt="{{ comment.author }}">
    </td>
    <td colspan="3">
        <form name="editform" class="uk-form uk-form-stacked" v-on="valid: submit">

            <div class="uk-grid uk-grid-width-small-1-3" data-uk-grid-margin>
                <div>
                    <label for="form-author" class="uk-form-label">{{ 'Name' | trans }}</label>
                    <input id="form-author" class="uk-width-1-1" name="author" type="text" v-model="comment.author" v-valid="required">
                    <p class="uk-form-help-block uk-text-danger" v-show="editform.author.invalid">{{ 'Author cannot be blank.' | trans }}</p>
                </div>
                <div>
                    <label for="form-email" class="uk-form-label">{{ 'E-mail' | trans }}</label>
                    <input id="form-email" class="uk-width-1-1" name="email" type="text" v-model="comment.email" lazy v-valid="email">
                    <p class="uk-form-help-block uk-text-danger" v-show="editform.email.invalid">{{ 'Field must be a valid email address.' | trans }}</p>
                </div>
                <div>
                    <label for="form-status" class="uk-form-label">{{ 'Status' | trans }}</label>
                    <select id="form-status" class="uk-width-1-1" v-model="comment.status" options="statuses | toOptions
                    "></select>
                </div>
            </div>

            <div class="uk-grid uk-grid-width-1-1">
                <div>
                    <textarea class="uk-width-1-1" name="content" v-model="comment.content" rows="6" v-valid="required"></textarea>
                    <p class="uk-form-help-block uk-text-danger" v-show="editform.content.invalid">{{ 'Content cannot be blank.' | trans }}</p>
                </div>
            </div>

            <p>
                <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>
                <button class="uk-button" v-on="click: cancel">{{ 'Cancel' | trans }}</button>
            </p>

        </form>
    </td>

</script>

<script id="comment.reply" type="text/template">

    <td></td>
    <td class="pk-table-width-minimum">
        <img v-gravatar="comment.email" class="uk-img-preserve uk-border-circle" width="40" height="40" alt="{{ comment.author }}">
    </td>
    <td colspan="3">
        <form name="replyform" class="uk-form" v-on="valid: submit">

            <div class="uk-form-row">
                <textarea class="uk-width-1-1" name="content" v-model="comment.content" rows="6" v-valid="required"></textarea>
                <p class="uk-form-help-block uk-text-danger" v-show="replyform.content.invalid">{{ 'Content cannot be blank.' | trans }}</p>
            </div>

            <p>
                <button class="uk-button uk-button-primary" type="submit">{{ 'Reply' | trans }}</button>
                <button class="uk-button" v-on="click: cancel">{{ 'Cancel' | trans }}</button>
            </p>

        </form>
    </td>

</script>
