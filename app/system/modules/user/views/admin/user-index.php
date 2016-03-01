<?php $view->script('user-index', 'system/user:app/bundle/user-index.js', 'vue') ?>

<div id="users" class="uk-form" v-cloak>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

            <h2 class="uk-margin-remove" v-if="!selected.length">{{ '{0} %count% Users|{1} %count% User|]1,Inf[ %count% Users' | transChoice count {count:count} }}</h2>

            <template v-else>

                <h2 class="uk-margin-remove">{{ '{1} %count% User selected|]1,Inf[ %count% Users selected' | transChoice selected.length {count:selected.length} }}</h2>

                <div class="uk-margin-left">
                    <ul class="uk-subnav pk-subnav-icon">
                        <li><a class="pk-icon-check pk-icon-hover" :title="'Activate' | trans" data-uk-tooltip="{delay: 500}" @click="status(1)"></a></li>
                        <li><a class="pk-icon-block pk-icon-hover" :title="'Block' | trans" data-uk-tooltip="{delay: 500}" @click="status(0)"></a></li>
                        <li><a class="pk-icon-delete pk-icon-hover" :title="'Delete' | trans" data-uk-tooltip="{delay: 500}" @click.prevent="remove" v-confirm="'Delete users?'"></a></li>
                    </ul>
                </div>

            </template>

            <div class="pk-search">
                <div class="uk-search">
                    <input class="uk-search-field" type="text" v-model="config.filter.search" debounce="300">
                </div>
            </div>

        </div>
        <div data-uk-margin>

            <a class="uk-button uk-button-primary" :href="$url.route('admin/user/edit')">{{ 'Add User' | trans }}</a>

        </div>
    </div>

    <div class="uk-overflow-container">
        <table class="uk-table uk-table-hover uk-table-middle">
            <thead>
                <tr>
                    <th class="pk-table-width-minimum"><input type="checkbox" v-check-all:selected.literal="input[name=id]" number></th>
                    <th colspan="2" v-order:username="config.filter.order">
                        {{ 'User' | trans }}
                    </th>
                    <th class="pk-table-width-100 uk-text-center">
                        <input-filter :title="$trans('Status')" :value.sync="config.filter.status" :options="statuses"></input-filter>
                    </th>
                    <th class="pk-table-width-200" v-order:email="config.filter.order">
                        {{ 'Email' | trans }}
                    </th>
                    <th class="pk-table-width-100">
                        <input-filter :title="$trans('Roles')" :value.sync="config.filter.role" :options="roles"></input-filter>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="check-item" v-for="user in users" :class="{'uk-active': active(user)}">
                    <td><input type="checkbox" name="id" :value="user.id"></td>
                    <td class="pk-table-width-minimum">
                        <img class="uk-img-preserve uk-border-circle" width="40" height="40" :alt="user.name" v-gravatar="user.email">
                    </td>
                    <td class="uk-text-nowrap">
                        <a :href="$url.route('admin/user/edit', { id: user.id })">{{ user.username }}</a>
                        <div class="uk-text-muted">{{ user.name }}</div>
                    </td>
                    <td class="uk-text-center">
                        <a href="#" :title="user.statusText" :class="{
                            'pk-icon-circle-success': user.login && user.status,
                            'pk-icon-circle-danger': !user.status,
                            'pk-icon-circle-primary': user.status
                        }" @click="toggleStatus(user)"></a>
                    </td>
                    <td>
                        <a :href="'mailto:'+user.email">{{ user.email }}</a> <i class="uk-icon-check" :title="'Verified Email Address' | trans" v-if="showVerified(user)"></i>
                    </td>
                    <td>
                        {{ showRoles(user) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <h3 class="uk-h1 uk-text-muted uk-text-center" v-show="users && !users.length">{{ 'No user found.' | trans }}</h3>

    <v-pagination :page.sync="config.page" :pages="pages" v-show="pages > 1 || page > 0"></v-pagination>

</div>
