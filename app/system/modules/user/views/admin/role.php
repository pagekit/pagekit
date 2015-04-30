<?php $view->style('role-index', 'app/system/modules/user/assets/css/user.css') ?>
<?php $view->script('role-index', 'app/system/modules/user/app/role.js', ['system', 'uikit']) ?>

<div id="js-role" class="uk-form" v-cloak>

    <div class="uk-grid" data-uk-grid-margin>
        <div class="uk-width-medium-1-4">

            <div class="uk-panel uk-panel-divider">
                <ul class="pk-nestable" data-uk-sortable="{ handleClass: 'pk-nestable-handle', childClass: 'pk-nestable-item' }">
                    <li v-repeat="role: rolesArray | orderBy 'priority'" v-ref="ordered">
                        <div class="pk-nestable-item uk-visible-hover" v-class="pk-active: current.id === role.id">
                            <div class="pk-nestable-handle"></div>
                            <ol class="uk-subnav pk-subnav-icon uk-hidden" v.show="!role.isLocked">
                                <li><a title="{{ 'Edit' | trans }}" v-on="click: edit(role)"><i class="uk-icon-pencil"></i></a></li>
                                <li><a title="{{ 'Delete' | trans }}" v-on="click: remove(role)"><i class="uk-icon-minus-circle"></i></a></li>
                            </ol>
                            <a v-on="click: config.role = role.id">{{ role.name }}</a>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="uk-panel uk-panel-divider">
                <a class="uk-button" v-on="click: edit()">{{ 'Add Role' | trans }}</a>
            </div>

        </div>
        <div class="uk-width-medium-3-4">

            <h2>{{ current.name }}</h2>

            <div class="uk-overflow-container uk-margin-large" v-repeat="group: permissions">
                <table class="uk-table uk-table-hover uk-table-middle">
                    <thead>
                        <tr>
                            <th class="pk-table-min-width-200">{{ $key }}</th>
                            <th class="pk-table-width-minimum"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-repeat="permission: group">
                            <td class="pk-table-text-break">
                                {{ permission.title | trans }}
                                <small class="uk-text-muted uk-display-block" v-if="permission.description">{{ permission.description | trans }}</small>
                            </td>
                            <td class="uk-text-center">

                                <span class="pk-checkbox-fake" v-show="showFakeCheckbox(current, $key)">
                                    <input type="checkbox" checked disabled>
                                    <span v-if="!current.isAdministrator" v-on="click: addPermission(current, $key)"></span>
                                </span>

                                <input type="checkbox" value="{{ $key }}" v-show="!showFakeCheckbox(current, $key)" v-checkbox="current.permissions">

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div id="modal-role" class="uk-modal">
        <form class="uk-modal-dialog uk-modal-dialog-slide" v-on="submit: update">

            <p>
                <input class="uk-width-1-1 uk-form-large" type="text" placeholder="{{ 'Enter Role Name' | trans }}" v-model="role.name">
            </p>

            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>
            <button class="uk-button uk-modal-close">{{ 'Cancel' | trans }}</button>

        </form>
    </div>

</div>
