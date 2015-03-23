<?php $view->style('comment-index', 'extensions/blog/assets/css/blog.admin.css') ?>
<?php $view->script('comment-index', 'extensions/blog/app/comment/index.js', ['vue-system', 'vue-validator', 'gravatar']) ?>

<div id="js-comments" class="uk-form" v-cloak>

    <?php $view->section()->start('toolbar', 'show') ?>

        <div class="uk-float-left">

            <a class="uk-button pk-button-danger" v-show="selected.length" v-on="click: remove">{{ 'Delete' | trans }}</a>

            <div class="uk-button-dropdown" v-show="selected.length" data-uk-dropdown="{ mode: 'click' }">
                <button class="uk-button" type="button">{{ 'More' | trans }} <i class="uk-icon-caret-down"></i></button>
                <div class="uk-dropdown uk-dropdown-small">
                    <ul class="uk-nav uk-nav-dropdown">
                        <li><a v-on="click: status(1)">{{ 'Approve' | trans }}</a></li>
                        <li><a v-on="click: status(0)">{{ 'Unapprove' | trans }}</a></li>
                        <li><a v-on="click: status(2)">{{ 'Mark as spam' | trans }}</a></li>
                    </ul>
                </div>
            </div>

        </div>
        <div class="uk-float-right uk-hidden-small">

            <select v-model="config.filter.status" options="statusesFilter"></select>
            <input type="text" v-model="config.filter.search" placeholder="{{ 'Search' | trans }}" lazy>

        </div>

    <?php $view->section()->end() ?>

    <p v-show="comments && !comments.length" class="uk-alert uk-alert-info">{{ 'No comments found.' | trans }}</p>

    <div v-show="comments.length" class="uk-overflow-container">
        <table class="uk-table">
            <thead>
                <tr>
                    <th class="pk-table-width-minimum"><input type="checkbox" v-check-all="selected: input[name=id]"></th>
                    <th class="pk-table-min-width-300" colspan="2">{{ 'Comment' | trans }}</th>
                    <th class="pk-table-width-100 uk-text-center">{{ 'Status' | trans }}</th>
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

    <v-pagination v-with="page: config.page, pages: pages" v-show="pages > 1"></v-pagination>

</div>

<script id="comment.default" type="text/tmpl">

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
                <a v-if="comment.post.isAccessible" v-attr="href: comment.post.url+'#comment-'+comment.id">{{ comment.created | date long }}</a>
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

<script id="comment.edit" type="text/tmpl">

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
                    <select id="form-status" class="uk-width-1-1" v-model="comment.status" options="statuses"></select>
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
                <button class="uk-button" v-on="mousedown: $event.preventDefault(), click: cancel()">{{ 'Cancel' | trans }}</button>
            </p>

        </form>
    </td>

</script>

<script id="comment.reply" type="text/tmpl">

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
                <button class="uk-button" v-on="mousedown: $event.preventDefault(), click: cancel()">{{ 'Cancel' | trans }}</button>
            </p>

        </form>
    </td>

</script>
