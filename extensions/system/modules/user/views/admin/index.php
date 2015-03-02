<?php $this['scripts']->queue('user-index', 'extensions/system/modules/user/app/index.js', ['vue-system', 'gravatar']) ?>

<div id="js-user" class="uk-form" v-cloak>

    <?php $this['sections']->start('toolbar', 'show') ?>

    <div class="uk-float-left">

        <a class="uk-button uk-button-primary" v-attr="href: $url('admin/system/user/edit')">{{ 'Add User' | trans }}</a>
        <a class="uk-button pk-button-danger" v-show="selected.length" v-on="click: remove">{{ 'Delete' | trans }}</a>

        <div class="uk-button-dropdown" v-show="selected.length" data-uk-dropdown="{ mode: 'click' }">
            <button class="uk-button" type="button">{{ 'More' | trans }} <i class="uk-icon-caret-down"></i></button>
            <div class="uk-dropdown uk-dropdown-small">
                <ul class="uk-nav uk-nav-dropdown">
                    <li><a v-on="click: status(1)">{{ 'Activate' | trans }}</a></li>
                    <li><a v-on="click: status(0)">{{ 'Block' | trans }}</a></li>
                </ul>
            </div>
        </div>

    </div>

    <div class="uk-float-right uk-hidden-small">

        <select v-model="config.filter.status" options="statuses"></select>
        <select v-model="config.filter.role" options="roles"></select>
        <select v-model="config.filter.permission" options="permissions"></select>
        <input type="text" v-model="config.filter.search" placeholder="{{ 'Search' | trans }}" lazy>

    </div>

    <?php $this['sections']->end() ?>

    <p v-show="!users.length" class="uk-alert uk-alert-info">{{ 'No user found.' | trans }}</p>

    <div v-show="users.length" class="uk-overflow-container">
        <table class="uk-table uk-table-hover uk-table-middle">
            <thead>
                <tr>
                    <th class="pk-table-width-minimum"><input type="checkbox" v-check-all="selected: input[name=id]"></th>
                    <th colspan="2">{{ 'User' | trans }}</th>
                    <th class="pk-table-width-100 uk-text-center">{{ 'Status' | trans }}</th>
                    <th class="pk-table-width-200">{{ 'Email' | trans }}</th>
                    <th class="pk-table-width-100">{{ 'Roles' | trans }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-repeat="user: users">
                    <td><input type="checkbox" name="id" value="{{ user.id }}"></td>
                    <td class="pk-table-width-minimum">
                        <img v-gravatar="user.email" class="uk-img-preserve uk-border-circle" width="40" height="40" alt="">
                    </td>
                    <td class="uk-text-nowrap">
                        <a v-attr="href: $url('admin/system/user/edit', { id: user.id})">{{ user.username }}</a>
                        <div class="uk-text-muted">{{ user.name }}</div>
                    </td>
                    <td class="uk-text-center">
                        <a href="#" v-class="
                            uk-text-success: !user.isNew && user.status,
                            uk-text-danger: !user.isNew && !user.status
                        " class="uk-icon-circle" v-on="click: toggleStatus(user)" title="{{ user.statusText }}"></a>
                    </td>
                    <td>
                        <a href="mailto:{{ user.email }}">{{ user.email }}</a> <i v-if="showVerified(user)" title="{{ 'Verified Email Address' | trans }}" class="uk-icon-check"></i>
                    </td>
                    <td>
                        {{ showRoles(user) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <v-pagination v-with="page: config.page, pages: pages" v-show="pages > 1"></v-pagination>

</div>
