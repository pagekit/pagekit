<?php $view->style('permission-index', 'app/modules/user/assets/css/user.css') ?>
<?php $view->script('permission-index', 'app/modules/user/app/role.js', ['vue-system', 'uikit-sticky']) ?>

<div id="js-permission" class="uk-form" v-cloak>

    <div class="uk-overflow-container">

        <table class="uk-table uk-table-hover uk-table-middle pk-table-subheading pk-table-indent uk-margin-remove" v-sticky-table-header>
            <thead>
                <tr>
                    <th class="pk-table-min-width-200">{{ 'Permission' | trans }}</th>
                    <th v-repeat="roles" class="pk-table-width-100 pk-table-max-width-100 uk-text-truncate uk-text-center">{{ name }}</th>
                </tr>
            </thead>
        </table>

        <table v-repeat="group: permissions" class="uk-table uk-table-hover uk-table-middle pk-table-subheading pk-table-indent">
            <thead>
                <tr>
                    <th class="pk-table-min-width-200">{{ $key }}</th>
                    <th v-repeat="roles" class="pk-table-width-100 pk-table-min-width-100"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-repeat="permission: group">
                    <td class="pk-table-text-break">
                        {{ permission.title | trans }}
                        <small v-if="permission.description" class="uk-text-muted uk-display-block">{{ permission.description | trans }}</small>
                    </td>
                    <td v-repeat="role: roles" class="uk-text-center">

                        <span v-show="showFakeCheckbox(role, $parent.$key)" class="pk-checkbox-fake">
                            <input type="checkbox" checked disabled>
                            <span v-if="!role.isAdministrator" v-on="click: addPermission(role, $parent.$key)"></span>
                        </span>

                        <input v-show="!showFakeCheckbox(role, $parent.$key)" type="checkbox" v-checkbox="role.permissions" value="{{ $parent.$key }}">
                    </td>
                </tr>
            </tbody>
        </table>

    </div>

</div>
