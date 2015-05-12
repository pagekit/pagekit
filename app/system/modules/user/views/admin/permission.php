<?php $view->script('permission-index', 'system/user:app/admin/role.js', ['system', 'uikit-sticky']) ?>

<div id="js-permission" class="uk-form" v-cloak>

    <h2>{{ 'Permissions' | trans }}</h2>

    <div class="uk-overflow-container uk-margin-large" v-repeat="group: permissions">
        <table class="uk-table uk-table-hover uk-table-middle">
            <thead>
                <tr>
                    <th class="pk-table-min-width-200">{{ $key }}</th>
                    <th class="pk-table-width-minimum pk-table-max-width-100 uk-text-truncate uk-text-center" v-repeat="roles">{{ name }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-repeat="permission: group">
                    <td class="pk-table-text-break">
                        {{ permission.title | trans }}
                        <small class="uk-text-muted uk-display-block" v-if="permission.description">{{ permission.description | trans }}</small>
                    </td>
                    <td class="uk-text-center" v-repeat="role: roles">

                        <span class="uk-position-relative" v-show="showFakeCheckbox(role, $parent.$key)">
                            <input type="checkbox" checked disabled>
                            <span class="uk-position-cover" v-if="!role.isAdministrator" v-on="click: addPermission(role, $parent.$key)"></span>
                        </span>

                        <input type="checkbox" value="{{ $parent.$key }}" v-show="!showFakeCheckbox(role, $parent.$key)" v-checkbox="role.permissions">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
