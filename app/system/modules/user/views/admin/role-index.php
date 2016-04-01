<?php $view->script('role-index', 'system/user:app/bundle/role-index.js', 'vue') ?>

<div id="roles" class="uk-form" v-cloak>

    <div class="uk-grid pk-grid-large" data-uk-grid-margin>
        <div class="pk-width-sidebar">

            <div class="uk-panel">

                <ul class="uk-sortable uk-nav uk-nav-side" data-uk-sortable="{dragCustomClass:'pk-sortable-dragged-list'}">
                    <li class="uk-visible-hover" v-for="role in roles | orderBy 'priority'" :class="{'uk-active': current.id === role.id}">
                        <ul class="uk-subnav pk-subnav-icon uk-hidden" v-if="!role.locked">
                            <li><a class="pk-icon-edit pk-icon-hover" :title="'Edit' | trans" data-uk-tooltip="{delay: 500}" @click="edit(role)"></a></li>
                            <li><a class="pk-icon-delete pk-icon-hover" :title="'Delete' | trans" data-uk-tooltip="{delay: 500}" @click="remove(role)" v-confirm="'Delete role?'"></a></li>
                        </ul>
                        <a @click.prevent="config.role = role.id">{{ role.name }}</a>
                    </li>
                </ul>

                <p>
                    <a class="uk-button" @click.prevent="edit()">{{ 'Add Role' | trans }}</a>
                </p>

            </div>

        </div>
        <div class="pk-width-content">

            <h2>{{ current.name }}</h2>

            <div class="uk-overflow-container uk-margin-large" v-for="group in permissions">
                <table class="uk-table uk-table-hover uk-table-middle">
                    <thead>
                        <tr>
                            <th class="pk-table-min-width-200">{{ $key }}</th>
                            <th class="pk-table-width-minimum"></th>
                            <th class="pk-table-width-minimum"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="permission in group" :class="{'uk-visible-hover-inline': permission.trusted}">
                            <td class="pk-table-text-break">
                                <span :title="permission.description | trans" data-uk-tooltip="{pos:'top-left'}">{{ permission.title | trans }}</span>
                            </td>
                            <td>
                                <i class="pk-icon-warning uk-invisible" :title="'Grant this permission to trusted roles only to avoid security implications.' | trans" data-uk-tooltip v-if="permission.trusted"></i>
                            </td>
                            <td class="uk-text-center">

                                <span class="uk-position-relative" v-show="showFakeCheckbox(current, $key)">
                                    <input type="checkbox" checked disabled>
                                    <span class="uk-position-cover" v-if="!current.administrator" @click="addPermission(current, $key)" @click="savePermissions(current)"></span>
                                </span>

                                <input type="checkbox" :value="$key" v-else v-model="current.permissions" @click="savePermissions(current)">

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <v-modal v-ref:modal>
        <form class="uk-form uk-form-stacked" v-validator="form" @submit.prevent.stop="save | valid">

            <div class="uk-modal-header">
                <h2>{{ (role.id ? 'Edit Role':'Add Role') | trans }}</h2>
            </div>

            <div class="uk-form-row">
                <label for="form-name" class="uk-form-label">{{ 'Name' | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form-name" class="uk-width-1-1 uk-form-large" type="text" name="name" v-model="role.name" v-validate:required>
                    <p class="uk-form-help-block uk-text-danger" v-show="form.name.invalid">{{ 'Name cannot be blank.' | trans }}</p>
                </div>
            </div>

            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-link uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
                <button class="uk-button uk-button-link" type="submit">{{ 'Save' | trans }}</button>
            </div>

        </form>
    </v-modal>

</div>
