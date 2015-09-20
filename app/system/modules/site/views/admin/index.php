<?php $view->script('site-index', 'system/site:app/bundle/index.js', ['vue', 'uikit-nestable']) ?>

<div id="site" class="uk-form" v-cloak>

    <div class="uk-grid pk-grid-large" data-uk-grid-margin>
        <div class="pk-width-sidebar">

            <div class="uk-panel">

                <ul class="uk-nav uk-nav-side">
                    <li class="uk-visible-hover" v-class="uk-active: isActive(menu), uk-nav-divider: menu.divider" v-repeat="menu: menus | divided">
                        <a v-on="click: selectMenu(menu, false)" v-if="!menu.divider">{{ menu.label }}</a>
                        <ul class="uk-subnav pk-subnav-icon uk-hidden" v-if="!menu.fixed && !menu.divider">
                            <li><a class="pk-icon-edit pk-icon-hover" title="{{ 'Edit' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: editMenu(menu)"></a></li>
                            <li><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: removeMenu(menu)" v-confirm="'Delete menu?'"></a></li>
                        </ul>
                    </li>
                </ul>

                <p>
                    <a class="uk-button" v-on="click: editMenu()">{{ 'Add Menu' | trans }}</a>
                </p>

            </div>

        </div>
        <div class="uk-flex-item-1">

            <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
                <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

                    <h2 class="uk-margin-remove">{{ menu.label | trans }}</h2>

                    <div class="uk-margin-left" v-show="selected.length">
                        <ul class="uk-subnav pk-subnav-icon">
                            <li><a class="pk-icon-check pk-icon-hover" title="{{ 'Publish' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: status(1)"></a></li>
                            <li><a class="pk-icon-block pk-icon-hover" title="{{ 'Unpublish' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: status(0)"></a></li>
                            <li v-show="showMove" data-uk-dropdown="{ mode: 'click' }">
                                <a class="pk-icon-move pk-icon-hover" title="{{ 'Move' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: $event.preventDefault()"></a>
                                <div class="uk-dropdown uk-dropdown-small">
                                    <ul class="uk-nav uk-nav-dropdown">
                                        <li v-repeat="menus | trash"><a v-on="click: moveNodes(id)">{{ label }}</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li v-show="showDelete"><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: removeNodes" v-confirm="'Delete item?'"></a></li>
                        </ul>
                    </div>

                </div>
                <div class="uk-position-relative" data-uk-margin>

                    <div data-uk-dropdown="{ mode: 'click' }">
                        <a class="uk-button uk-button-primary" v-on="click: $event.preventDefault()">{{ 'Add Page' | trans }}</a>
                        <div class="uk-dropdown uk-dropdown-small uk-dropdown-flip">
                            <ul class="uk-nav uk-nav-dropdown">
                                <li v-repeat="types | protected | orderBy 'label'"><a v-attr="href: $url.route('admin/site/page/edit', { id: id, menu: menu.id })">{{ label }}</a></li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>

            <div class="uk-overflow-container">

                <div class="pk-table-fake pk-table-fake-header" v-class="pk-table-fake-border: !tree[0]">
                    <div class="pk-table-width-minimum pk-table-fake-nestable-padding"><input type="checkbox" v-check-all="selected: input[name=id]" number></div>
                    <div class="pk-table-min-width-100">{{ 'Title' | trans }}</div>
                    <div class="pk-table-width-100 uk-text-center">{{ 'Status' | trans }}</div>
                    <div class="pk-table-width-100">{{ 'Type' | trans }}</div>
                    <div class="pk-table-width-150">{{ 'URL' | trans }}</div>
                </div>

                <ul class="uk-nestable uk-margin-remove" v-el="nestable" v-show="tree[0]">
                    <node v-repeat="node: tree[0]"></node>
                </ul>

            </div>

            <h3 class="uk-h1 uk-text-muted uk-text-center" v-show="!tree[0]">{{ 'No pages found.' | trans }}</h3>

        </div>
    </div>

    <v-modal v-ref="modal">
        <form class="uk-form uk-form-stacked" name="form" v-on="submit: saveMenu(edit) | valid">

            <div class="uk-modal-header">
                <h2>{{ 'Add Menu' | trans }}</h2>
            </div>

            <div class="uk-form-row">
                <label for="form-name" class="uk-form-label">{{ 'Name' | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form-name" class="uk-width-1-1 uk-form-large" name="label" type="text" v-model="edit.label | trim" v-valid="required">
                    <p class="uk-form-help-block uk-text-danger" v-show="form.label.invalid">{{ 'Invalid name.' | trans }}</p>
                </div>
            </div>

            <div class="uk-form-row">
                <span class="uk-form-label">{{ 'Menu Positions' | trans }}</span>
                <div class="uk-form-controls uk-form-controls-text">
                    <p class="uk-form-controls-condensed" v-repeat="config.menus" v-var="assigned: getMenu(name)">
                        <label><input type="checkbox" value="{{ name }}" v-checkbox="edit.positions"> {{ label }}</label> <span class="uk-text-muted" v-if="assigned && assigned.id != edit.id">{{ '(Currently set to: %menu%)' | trans {menu:assigned.label} }}</span>
                    </p>
                </div>
            </div>

            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-link uk-modal-close" type="button" v-on="click: cancel">{{ 'Cancel' | trans }}</button>
                <button class="uk-button uk-button-link" v-attr="disabled: form.invalid || !edit.label">{{ 'Save' | trans }}</button>
            </div>

        </form>
    </v-modal>

</div>

<script id="node" type="text/template">

    <li class="uk-nestable-item" v-class="uk-parent: tree[node.id], uk-active: isSelected(node)" data-id="{{ node.id }}">
        <div class="uk-nestable-panel pk-table-fake uk-form uk-visible-hover">
            <div class="pk-table-width-minimum pk-table-collapse">
                <div class="uk-nestable-toggle" data-nestable-action="toggle"></div>
            </div>
            <div class="pk-table-width-minimum"><input type="checkbox" name="id" value="{{ node.id }}"></div>
            <div class="pk-table-min-width-100">
                <a v-attr="href: $url.route('admin/site/page/edit', { id: node.id })">{{ node.title }}</a>
                <span class="uk-text-muted uk-text-small uk-margin-small-left" v-if="node.data.menu_hide">{{ 'Hidden' | trans }}</span>
            </div>
            <div class="pk-table-width-minimum">
                <a class="pk-icon-home pk-icon-hover uk-invisible" title="{{ 'Set as frontpage' | trans }}" data-uk-tooltip="{delay: 500}" v-if="!isFrontpage && node.status && type.frontpage !== false" v-on="click: setFrontpage(node)"></a>
                <i class="pk-icon-home-active pk-icon-muted uk-float-right" title="{{ 'Frontpage' | trans }}" v-if="isFrontpage"></i>
            </div>
            <div class="pk-table-width-100 uk-text-center">
                <td class="uk-text-center">
                    <a v-class="pk-icon-circle-danger: !node.status, pk-icon-circle-success: node.status" v-on="click: toggleStatus(node)"></a>
                </td>
            </div>
            <div class="pk-table-width-100">{{ type.label }}</div>
            <div class="pk-table-width-150 pk-table-max-width-150 uk-text-truncate">
                <a title="{{ node.url }}" target="_blank" v-attr="href: $url.route(node.url.substr(1))" v-if="node.accessible && node.url">{{ node.url }}</a>
                <span v-if="!node.accessible || !node.url">{{ node.path }}</span>
            </div>
        </div>

        <ul class="uk-nestable-list" v-if="tree[node.id]">
            <node v-repeat="node: tree[node.id]" track-by="node.id"></node>
        </ul>

    </li>

</script>
