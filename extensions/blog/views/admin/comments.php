<?php $view->style('comment-index', 'blog:assets/css/blog.admin.css') ?>
<?php $view->script('comment-index', 'blog:app/bundle/admin/comments.js', 'vue') ?>

<div id="comments" class="uk-form" data-uk-observe v-cloak>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

            <h2 class="uk-margin-remove" v-show="!selected.length">{{ '{0} %count% Comments|{1} %count% Comment|]1,Inf[ %count% Comments' | transChoice count {count:count} }}</h2>
            <h2 class="uk-margin-remove" v-show="selected.length">{{ '{1} %count% Comment selected|]1,Inf[ %count% Comments selected' | transChoice selected.length {count:selected.length} }}</h2>

            <div class="uk-margin-left" v-show="selected.length">
                <ul class="uk-subnav pk-subnav-icon">
                    <li><a class="pk-icon-check pk-icon-hover" title="{{ 'Approve' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: status(1)"></a></li>
                    <li><a class="pk-icon-block pk-icon-hover" title="{{ 'Unapprove' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: status(0)"></a></li>
                    <li><a class="pk-icon-spam pk-icon-hover" title="{{ 'Mark as spam' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: status(2)"></a></li>
                    <li><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-show="selected.length" v-on="click: remove"></a></li>
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
                    <th class="pk-table-width-200" v-class="pk-filter: config.post, uk-active: config.post">
                        <span v-if="!config.post">{{ 'Post' | trans }}</span>
                        <span v-if="config.post">{{ config.post.title }}</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-repeat="comment: comments" v-component="comments-row"></tr>
            </tbody>
        </table>
    </div>

    <p class="uk-alert uk-alert-info" v-show="comments && !comments.length">{{ 'No comments found.' | trans }}</p>

    <v-pagination page="{{ config.page }}" pages="{{ pages }}" v-show="pages > 1"></v-pagination>

</div>
