<?php $view->script('role-index', 'system/user:app/bundle/admin/roles.js', 'vue') ?>

<div id="roles" class="uk-form" v-cloak>

    <div class="uk-grid pk-grid-large" data-uk-grid-margin>
        <div class="pk-width-sidebar">

            <div class="uk-panel">

                <ul class="pk-nestable" data-uk-sortable="{ handleClass: 'pk-nestable-handle', childClass: 'pk-nestable-item' }">
                    <li v-repeat="role: roles | orderBy 'priority'" v-ref="ordered">
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

                <p>
                    <a class="uk-button" v-on="click: edit()">{{ 'Add Role' | trans }}</a>
                </p>

            </div>

        </div>
        <div class="uk-flex-item-1">

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

                                <span class="uk-position-relative" v-show="showFakeCheckbox(current, $key)">
                                    <input type="checkbox" checked disabled>
                                    <span class="uk-position-cover" v-if="!current.isAdministrator" v-on="click: addPermission(current, $key), click: savePermissions(current)"></span>
                                </span>

                                <input type="checkbox" value="{{ $key }}" v-show="!showFakeCheckbox(current, $key)" v-checkbox="current.permissions" v-on="click: savePermissions(current)">

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div id="modal-role" class="uk-modal">
        <form class="uk-modal-dialog uk-form-stacked" v-on="submit: save">

            <div class="uk-modal-header">
                <h2>{{ 'Add Role' | trans }}</h2>
            </div>

            <div class="uk-form-row">
                <label for="form-name" class="uk-form-label">{{ 'Name' | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form-name" class="uk-width-1-1 uk-form-large" type="text" v-model="role.name">
                </div>
            </div>

            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-link uk-modal-close">{{ 'Cancel' | trans }}</button>
                <button class="uk-button uk-button-link" type="submit">{{ 'Save' | trans }}</button>
            </div>

        </form>
    </div>

</div>
