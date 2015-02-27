<?php
    $this['styles']->queue('permission-index', 'extensions/system/assets/css/user.css');
    $this['scripts']->queue('permission-index', 'extensions/system/modules/user/app/permission.js', ['vue-system', 'uikit-sticky']);
?>

<div id="js-permission" class="uk-form" v-cloak>

    <div class="uk-overflow-container">

        <table class="uk-table uk-table-hover uk-table-middle pk-table-subheading pk-table-indent uk-margin-remove">
            <thead>
                <tr>
                    <th class="pk-table-min-width-200">{{ 'Permission' | trans }}</th>
                    <th v-repeat="roles" class="pk-table-width-100 pk-table-max-width-100 uk-text-truncate uk-text-center">{{ name }}</th>
                </tr>
            </thead>
        </table>
        <table v-repeat="group: permissions" class="uk-table uk-table-hover uk-table-middle pk-table-subheading pk-table-indent uk-margin-remove">
            <tbody>
                <tr>
                    <th class="pk-table-min-width-200">{{ $key }}</th>
                    <th v-repeat="roles" class="pk-table-width-100 pk-table-min-width-100"></th>
                </tr>
                <tr v-repeat="permission: group">
                    <td class="pk-table-text-break">
                        {{ permission.title | trans }}
                        <small v-if="permission.description" class="uk-text-muted uk-display-block">{{ permission.description | trans }}</small>
                    </td>
                    <td v-repeat="role: roles" class="uk-text-center">
                        <input v-if="role.isAdministrator" type="checkbox" checked disabled>
                        <input v-if="!role.isAdministrator" v-class="pk-checkbox: role.isLocked" type="checkbox" v-checkbox="role.permissions" value="{{ $parent.$key }}">
                    </td>
                </tr>
            </tbody>
        </table>

        <pre>{{ roles | json }}</pre>
        <pre>{{ permissions | json }}</pre>

    </div>

</div>
