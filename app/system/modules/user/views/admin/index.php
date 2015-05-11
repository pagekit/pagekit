<?php $view->script('user-index', 'system/user:app/index.js', ['system', 'gravatar']) ?>

<div id="js-user" class="uk-form" v-cloak>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

            <h2 class="uk-margin-remove">{{ $trans('%count% Users', {count: count}) }}</h2>

            <div class="uk-margin-left" v-show="selected.length">
                <ul class="uk-subnav pk-subnav-icon">
                    <li><a class="uk-icon-trash-o" v-on="click: remove"></a></li>
                    <li><a class="uk-icon-check-circle-o" v-on="click: status(1)"></a></li>
                    <li><a class="uk-icon-ban" v-on="click: status(0)"></a></li>
                </ul>
            </div>

            <div class="pk-search">
                <div class="uk-search">
                    <input class="uk-search-field" type="text" v-model="config.filter.search" debounce="300">
                </div>
            </div>

        </div>
        <div data-uk-margin>

            <a class="uk-button uk-button-primary" v-attr="href: $url('admin/user/edit')">{{ 'Add User' | trans }}</a>

        </div>
    </div>

    <div class="uk-overflow-container">
        <table class="uk-table uk-table-hover uk-table-middle">
            <thead>
                <tr>
                    <th class="pk-table-width-minimum"><input type="checkbox" v-check-all="selected: input[name=id]"></th>
                    <th colspan="2" class="pk-table-sort">{{ 'User' | trans }} <i class="uk-icon uk-icon-long-arrow-up uk-margin-small-left"></i></th>
                    <th class="pk-table-width-100 uk-text-center">
                        <div class="uk-form-select pk-filter" data-uk-form-select>
                            <span>{{ 'Status' | trans }}</span>
                            <select v-model="config.filter.status" options="statuses"></select>
                        </div>
                    </th>
                    <th class="pk-table-width-200 pk-table-sort">{{ 'Email' | trans }}</th>
                    <th class="pk-table-width-100">
                        <div class="uk-form-select pk-filter" data-uk-form-select>
                            <span>{{ 'Roles' | trans }}</span>
                            <select v-model="config.filter.role" options="roles"></select>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-repeat="user: users" v-class="uk-active: active(user)">
                    <td><input type="checkbox" name="id" value="{{ user.id }}"></td>
                    <td class="pk-table-width-minimum">
                        <img class="uk-img-preserve uk-border-circle" width="40" height="40" alt="" v-gravatar="user.email">
                    </td>
                    <td class="uk-text-nowrap">
                        <a v-attr="href: $url('admin/user/edit', { id: user.id})">{{ user.username }}</a>
                        <div class="uk-text-muted">{{ user.name }}</div>
                    </td>
                    <td class="uk-text-center">
                        <a class="uk-icon-circle" href="#" title="{{ user.statusText }}" v-class="
                            uk-text-success: !user.isNew && user.status,
                            uk-text-danger: !user.isNew && !user.status
                        " v-on="click: toggleStatus(user)"></a>
                    </td>
                    <td>
                        <a href="mailto:{{ user.email }}">{{ user.email }}</a> <i class="uk-icon-check" title="{{ 'Verified Email Address' | trans }}" v-if="showVerified(user)"></i>
                    </td>
                    <td>
                        {{ showRoles(user) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <p class="uk-alert uk-alert-info" v-show="users && !users.length">{{ 'No user found.' | trans }}</p>

    <v-pagination v-with="page: config.page, pages: pages" v-show="pages > 1"></v-pagination>

</div>
