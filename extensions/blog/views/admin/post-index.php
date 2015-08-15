<?php $view->script('post-index', 'blog:app/bundle/post-index.js', 'vue') ?>

<div id="post" class="uk-form" v-cloak>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

            <h2 class="uk-margin-remove" v-show="!selected.length">{{ '{0} %count% Posts|{1} %count% Post|]1,Inf[ %count% Posts' | transChoice count {count:count} }}</h2>
            <h2 class="uk-margin-remove" v-show="selected.length">{{ '{1} %count% Post selected|]1,Inf[ %count% Posts selected' | transChoice selected.length {count:selected.length} }}</h2>

            <div class="uk-margin-left" v-show="selected.length">
                <ul class="uk-subnav pk-subnav-icon">
                    <li><a class="pk-icon-check pk-icon-hover" title="Publish" data-uk-tooltip="{delay: 500}" v-on="click: status(2)"></a></li>
                    <li><a class="pk-icon-block pk-icon-hover" title="Unpublish" data-uk-tooltip="{delay: 500}" v-on="click: status(3)"></a></li>
                    <li><a class="pk-icon-copy pk-icon-hover" title="Copy" data-uk-tooltip="{delay: 500}" v-on="click: copy"></a></li>
                    <li><a class="pk-icon-delete pk-icon-hover" title="Delete" data-uk-tooltip="{delay: 500}" v-on="click: remove" v-confirm="'Delete Posts?'"></a></li>
                </ul>
            </div>

            <div class="pk-search">
                <div class="uk-search">
                    <input class="uk-search-field" type="text" v-model="config.filter.search" debounce="300">
                </div>
            </div>

        </div>
        <div data-uk-margin>

            <a class="uk-button uk-button-primary" v-attr="href: $url.route('admin/blog/post/edit')">{{ 'Add Post' | trans }}</a>

        </div>
    </div>

    <div class="uk-overflow-container">
        <table class="uk-table uk-table-hover uk-table-middle">
            <thead>
                <tr>
                    <th class="pk-table-width-minimum"><input type="checkbox" v-check-all="selected: input[name=id]" number></th>
                    <th class="pk-table-min-width-200" v-order="title: config.filter.order">{{ 'Title' | trans }}</th>
                    <th class="pk-table-width-100 uk-text-center">
                        <input-filter title="{{ 'Status' | trans }}" value="{{@ config.filter.status}}" options="{{ statusOptions }}"></input-filter>
                    </th>
                    <th class="pk-table-width-100">
                        <span v-if="!canEditAll">{{ 'Author' | trans }}</span>
                        <input-filter title="{{ 'Author' | trans }}" value="{{@ config.filter.author}}" options="{{ authors }}" v-if="canEditAll"></input-filter>
                    </th>
                    <th class="pk-table-width-100 uk-text-center" v-order="comment_count: config.filter.order">{{ 'Comments' | trans }}</th>
                    <th class="pk-table-width-100" v-order="date: config.filter.order">{{ 'Date' | trans }}</th>
                    <th class="pk-table-width-200 pk-table-min-width-200">{{ 'URL' | trans }}</th>
                </tr>
            </thead>
            <tbody>
                <tr class="check-item" v-repeat="post: posts" v-class="uk-active: active(post)">
                    <td><input type="checkbox" name="id" value="{{ post.id }}"></td>
                    <td>
                        <a v-attr="href: $url.route('admin/blog/post/edit', { id: post.id })">{{ post.title }}</a>
                    </td>
                    <td class="uk-text-center">
                        <a title="{{ getStatusText(post) }}" v-class="
                                pk-icon-circle: post.status == 0,
                                pk-icon-circle-warning: post.status == 1,
                                pk-icon-circle-success: post.status == 2 && post.published,
                                pk-icon-circle-danger: post.status == 3,
                                pk-icon-schedule: post.status == 2 && !post.published
                            " v-on="click: toggleStatus(post)"></a>
                    </td>
                    <td>
                        <a v-attr="href: $url.route('admin/user/edit', { id: post.user_id })">{{ post.author }}</a>
                    </td>
                    <td class="uk-text-center">
                        <a class="uk-text-nowrap" v-class="pk-link-icon: !post.comments_pending" v-attr="href: $url.route('admin/blog/comment', { post: post.id })" title="{{ '{0} No pending|{1} One pending|]1,Inf[ %comments% pending' | transChoice post.comments_pending {comments:post.comments_pending} }}"><i class="pk-icon-comment uk-margin-small-right" v-class="pk-icon-primary: post.comments_pending"></i> {{ post.comment_count }}</a>
                    </td>
                    <td>
                        {{ post.date | date }}
                    </td>
                    <td class="pk-table-text-break">
                        <a target="_blank" v-if="post.accessible && post.url" v-attr="href: this.$url.route(post.url.substr(1))">{{ post.url }}</a>
                        <span v-if="!post.accessible && post.url">{{ post.url }}</span>
                        <span v-if="!post.url">{{ 'Disabled' | true}}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <h3 class="uk-h1 uk-text-muted uk-text-center" v-show="posts && !posts.length">{{ 'No posts found.' | trans }}</h3>

    <v-pagination page="{{@ config.page }}" pages="{{ pages }}" v-show="pages > 1"></v-pagination>

</div>
