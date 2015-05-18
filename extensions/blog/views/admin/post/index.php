<?php $view->script('post-index', 'blog:app/admin/post/index.js', 'system') ?>

<div id="js-post" class="uk-form" v-cloak>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

            <h2 class="uk-margin-remove" v-show="!selected.length">{{ '{0} %count% Posts|{1} %count% Post|]1,Inf[ %count% Posts' | transChoice count {count:count} }}</h2>
            <h2 class="uk-margin-remove" v-show="selected.length">{{ '{1} %count% Post selected|]1,Inf[ %count% Posts selected' | transChoice selected.length {count:selected.length} }}</h2>

            <div class="uk-margin-left" v-show="selected.length">
                <ul class="uk-subnav pk-subnav-icon">
                    <li><a class="uk-icon-trash-o" title="Delete" data-uk-tooltip="{delay: 500}" v-show="selected.length" v-on="click: remove"></a></li>
                    <li><a class="uk-icon-copy" title="Copy" data-uk-tooltip="{delay: 500}" v-on="click: copy"></a></li>
                    <li><a class="uk-icon-check-circle-o" title="Publish" data-uk-tooltip="{delay: 500}" v-on="click: status(2)"></a></li>
                    <li><a class="uk-icon-ban" title="Unpublish" data-uk-tooltip="{delay: 500}" v-on="click: status(3)"></a></li>
                </ul>
            </div>

            <div class="pk-search">
                <div class="uk-search">
                    <input class="uk-search-field" type="text" v-model="config.filter.search" debounce="300">
                </div>
            </div>

        </div>
        <div data-uk-margin>

            <a class="uk-button uk-button-primary" v-attr="href: $url('admin/blog/post/edit')">{{ 'Add Post' | trans }}</a>

        </div>
    </div>

    <div class="uk-overflow-container">
        <table class="uk-table uk-table-hover uk-table-middle">
            <thead>
                <tr>
                    <th class="pk-table-width-minimum"><input type="checkbox" v-check-all="selected: input[name=id]"></th>
                    <th class="pk-table-min-width-200">{{ 'Title' | trans }}</th>
                    <th class="pk-table-width-100 uk-text-center">
                        <div class="uk-form-select pk-filter" data-uk-form-select>
                            <span>{{ 'Status' | trans }}</span>
                            <select v-model="config.filter.status" options="statusOptions"></select>
                        </div>
                    </th>
                    <th class="pk-table-width-100">{{ 'Author' | trans }}</th>
                    <th class="pk-table-width-100 uk-text-center">{{ 'Comments' | trans }}</th>
                    <th class="pk-table-width-100">{{ 'Date' | trans }}</th>
                    <th class="pk-table-width-200 pk-table-min-width-200">{{ 'URL' | trans }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-repeat="post: posts">
                    <td><input type="checkbox" name="id" value="{{ post.id }}"></td>
                    <td>
                        <a v-attr="href: $url('admin/blog/post/edit', { id: post.id })">{{ post.title }}</a>
                    </td>
                    <td class="uk-text-center">
                        <a title="{{ getStatusText(post) }}" v-class="
                                uk-text-muted:   post.status == 0,
                                uk-text-warning: post.status == 1,
                                uk-text-success: post.status == 2,
                                uk-text-danger:  post.status == 3,
                                uk-icon-circle:  post.status != 2 || !post.isPublished,
                                uk-icon-clock-o: post.status == 2 && post.isPublished
                            " v-on="click: toggleStatus(post)"></a>
                    </td>
                    <td>
                        <a v-attr="href: $url('admin/user/edit', { id: post.user_id })">{{ post.author }}</a>
                    </td>
                    <td class="uk-text-center">
                        <a class="uk-badge uk-badge-notification" v-class="pk-badge: post.comments_pending" v-attr="href: $url('admin/blog/comment', { post: post.id })" title="{{ '{0} No pending|{1} One pending|]1,Inf[ %comments% pending' | transChoice post.comments_pending {comments:post.comments_pending} }}">{{ post.comment_count }}</a>
                    </td>
                    <td>
                        {{ post.date | date medium }}
                    </td>
                    <td class="pk-table-text-break">
                        <a target="_blank" v-if="post.isAccessible" v-attr="href: post.url">{{ post.url | baseUrl }}</a>
                        <span v-if="!post.isAccessible">{{ post.url | baseUrl }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <p class="uk-alert uk-alert-info" v-show="!posts.length">{{ 'No posts found.' | trans }}</p>

    <v-pagination page="{{ config.page }}" pages="{{ pages }}" v-show="pages > 1"></v-pagination>

</div>
