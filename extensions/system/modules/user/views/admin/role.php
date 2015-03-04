<div id="js-role" class="uk-form uk-grid uk-grid-divider" data-uk-grid-margin data-uk-grid-match v-cloak >

    <div class="uk-width-medium-1-4 pk-sidebar-left">

        <div class="uk-panel uk-panel-divider pk-panel-marginless">
            <ul class="pk-nestable" data-uk-sortable="{ handleClass: 'pk-nestable-handle', childClass: 'pk-nestable-item' }">
                <li v-repeat="role: rolesArray | orderBy 'priority'" v-ref="ordered">
                    <div class="pk-nestable-item uk-visible-hover" v-class="pk-active: current.id === role.id">
                        <div class="pk-nestable-handle"></div>
                        <ol v.show="!role.isLocked" class="uk-subnav pk-subnav-icon uk-hidden">
                            <li><a v-on="click: edit(role)" title="{{ 'Edit' | trans }}"><i class="uk-icon-pencil"></i></a></li>
                            <li><a v-on="click: remove(role)" title="{{ 'Delete' | trans }}"><i class="uk-icon-minus-circle"></i></a></li>
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

        <table class="uk-table uk-table-hover uk-table-middle pk-table-subheading pk-table-indent uk-margin-remove">
            <thead>
                <tr>
                    <th class="pk-table-min-width-200">{{ 'Permission' | trans }}</th>
                    <th class="pk-table-width-minimum"></th>
                </tr>
            </thead>
        </table>

        <table v-repeat="group: permissions" class="uk-table uk-table-hover uk-table-middle pk-table-subheading pk-table-indent uk-margin-remove">
            <tbody>
                <tr>
                    <th>{{ $key }}</th>
                    <th class="pk-table-width-200"></th>
                </tr>
                <tr v-repeat="permission: group">
                    <td class="pk-table-text-break">
                        {{ permission.title | trans }}
                        <small v-if="permission.description" class="uk-text-muted uk-display-block">{{ permission.description | trans }}</small>
                    </td>
                    <td class="uk-text-center">

                        <span v-show="showFakeCheckbox(current, $key)" class="pk-checkbox-fake">
                            <input type="checkbox" checked disabled>
                            <span v-if="!current.isAdministrator" v-on="click: addPermission(current, $key)"></span>
                        </span>

                        <input v-show="!showFakeCheckbox(current, $key)" type="checkbox" v-checkbox="current.permissions" value="{{ $key }}">

                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="modal-role" class="uk-modal">
        <form class="uk-modal-dialog uk-modal-dialog-slide" v-on="submit: update">

            <p>
                <input class="uk-width-1-1 uk-form-large" type="text" v-model="role.name" placeholder="{{ 'Enter Role Name' | trans }}">
            </p>

            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>
            <button class="uk-button uk-modal-close">{{ 'Cancel' | trans }}</button>

        </form>
    </div>

</div>
