<?php $view->script('post-index', 'extensions/blog/app/post/index.js', ['vue-system', 'uikit-pagination']) ?>

<div id="js-post" class="uk-form" v-cloak>

    <?php $view->section()->start('toolbar') ?>

    <div class="uk-float-left">

        <a class="uk-button uk-button-primary" v-attr="href: $url('admin/blog/post/edit')">{{ 'Add Post' | trans }}</a>
        <a class="uk-button pk-button-danger" v-show="selected.length" v-on="click: remove">{{ 'Delete' | trans }}</a>

        <div class="uk-button-dropdown" v-show="selected.length" data-uk-dropdown="{ mode: 'click' }">
            <button class="uk-button" type="button">{{ 'More' | trans }} <i class="uk-icon-caret-down"></i></button>
            <div class="uk-dropdown uk-dropdown-small">
                <ul class="uk-nav uk-nav-dropdown">
                    <li><a v-on="click: status(2)">{{ 'Publish' | trans }}</a></li>
                    <li><a v-on="click: status(3)">{{ 'Unpublish' | trans }}</a></li>
                    <li class="uk-nav-divider"></li>
                    <li><a  v-on="click: copy">{{ 'Copy' | trans }}</a></li>
                </ul>
            </div>
        </div>

    </div>
    <div class="uk-float-right uk-hidden-small">

        <select v-model="config.filter.status" options="statuses"></select>
        <input type="text" v-model="config.filter.search" placeholder="{{ 'Search' | trans }}" lazy>

    </div>

    <?php $view->section()->stop(true) ?>

    <p v-show="!posts.length" class="uk-alert uk-alert-info">{{ 'No posts found.' | trans }}</p>

    <div v-show="posts.length" class="uk-overflow-container">
        <table class="uk-table uk-table-hover uk-table-middle">
            <thead>
                <tr>
                    <th class="pk-table-width-minimum"><input type="checkbox" v-check-all="selected: input[name=id]"></th>
                    <th class="pk-table-min-width-200">{{ 'Title' | trans }}</th>
                    <th class="pk-table-width-100 uk-text-center">{{ 'Status' | trans }}</th>
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

                        <a v-on="click: toggleStatus(post)" title="{{ getStatusText(post) }}">
                            <i v-class="
                                uk-text-muted:   post.status == 0,
                                uk-text-warning: post.status == 1,
                                uk-text-success: post.status == 2,
                                uk-text-danger:  post.status == 3,
                                uk-icon-circle:  post.status != 2 || !post.isPublished,
                                uk-icon-clock-o: post.status == 2 && post.isPublished
                            "></i>
                        </a>
                    </td>
                    <td>
                        <a v-attr="href: $url('admin/system/user/edit', { id: post.user_id })">{{ post.author }}</a>
                    </td>
                    <td class="uk-text-center">
                        <a class="uk-badge uk-badge-notification" v-class="pk-badge: post.comments_pending" v-attr="href: $url('admin/blog/comment', { post: post.id })" title="{{ '{0} No pending|{1} One pending|]1,Inf[ %comments% pending' | transChoice post.comments_pending {comments:post.comments_pending} }}">{{ post.comment_count }}</a>
                    </td>
                    <td>
                        {{ post.date | date long }}
                    </td>
                    <td class="pk-table-text-break">
                        <a v-if="post.isAccessible" v-attr="href: post.url" target="_blank">{{ post.url | baseUrl }}</a>
                        <span v-if="!post.isAccessible">{{ post.url | baseUrl }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <v-pagination v-with="page: config.page, pages: pages" v-show="pages > 1"></v-pagination>

</div>
