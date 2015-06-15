<?php $view->script('dashboard', 'system/dashboard:app/bundle/index.js', ['vue', 'uikit-autocomplete']) ?>

<div id="dashboard">

    <div class="uk-margin uk-flex uk-flex-right" data-uk-margin>
        <div class="uk-button-dropdown" data-uk-dropdown="{ mode: 'click' }">
            <a class="uk-button uk-button-primary" v-on="click: $event.preventDefault()">{{ 'Add Widget' || trans }}</a>
            <div class="uk-dropdown uk-dropdown-small uk-dropdown-flip">
                <ul class="uk-nav uk-nav-dropdown">
                    <li v-repeat="type: getTypes()">
                        <a v-on="click: add(type)">{{ type.label }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="uk-grid pk-grid-small uk-grid-medium uk-grid-match" data-uk-grid-margin>
        <div class="uk-width-medium-1-3">

            <ul class="uk-sortable pk-sortable" data-column="0">
                <li v-repeat="widget: widgets | column 0" data-id="{{ widget.id }}" data-idx="{{ widget.idx }}">

                    <div class="uk-panel uk-panel-box uk-visible-hover-inline">

                        <div v-component="widget-panel" inline-template>

                            <div class="uk-panel-badge">
                                <ul class="uk-subnav pk-subnav-icon">
                                    <li v-if="editing[widget.id]"><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: remove()" v-confirm="'Delete widget?'"></a></li>
                                    <li v-if="type.editable !== false && !editing[widget.id]"><a class="pk-icon-edit pk-icon-hover uk-hidden" title="{{ 'Edit' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: edit()"></a></li>
                                    <li v-if="type.editable !== false && editing[widget.id]"><a class="pk-icon-check pk-icon-hover" title="{{ 'Confirm' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: edit()"></a></li>
                                </ul>
                            </div>

                            <div v-component="{{ component }}" v-with="widget: widget, editing: isEditing"></div>

                        </div>

                    </div>
                </li>
            </ul>

        </div>
        <div class="uk-width-medium-1-3">

            <ul class="uk-sortable pk-sortable" data-column="1">
                <li v-repeat="widget: widgets | column 1" data-id="{{ widget.id }}" data-idx="{{ widget.idx }}">

                    <div class="uk-panel uk-panel-box uk-visible-hover-inline">

                        <div v-component="widget-panel" inline-template>

                            <div class="uk-panel-badge">
                                <ul class="uk-subnav pk-subnav-icon">
                                    <li v-if="editing[widget.id]"><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: remove()" v-confirm="'Delete widget?'"></a></li>
                                    <li v-if="type.editable !== false && !editing[widget.id]"><a class="pk-icon-edit pk-icon-hover uk-hidden" title="{{ 'Edit' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: edit()"></a></li>
                                    <li v-if="type.editable !== false && editing[widget.id]"><a class="pk-icon-check pk-icon-hover" title="{{ 'Confirm' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: edit()"></a></li>
                                </ul>
                            </div>

                            <div v-component="{{ component }}" v-with="widget: widget, editing: isEditing"></div>

                        </div>

                    </div>
                </li>
            </ul>

        </div>
        <div class="uk-width-medium-1-3">

            <ul class="uk-sortable pk-sortable" data-column="2">
                <li v-repeat="widget: widgets | column 2" data-id="{{ widget.id }}" data-idx="{{ widget.idx }}">

                    <div class="uk-panel uk-panel-box uk-visible-hover-inline">

                        <div v-component="widget-panel" inline-template>

                            <div class="uk-panel-badge">
                                <ul class="uk-subnav pk-subnav-icon">
                                    <li v-if="editing[widget.id]"><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: remove()" v-confirm="'Delete widget?'"></a></li>
                                    <li v-if="type.editable !== false && !editing[widget.id]"><a class="pk-icon-edit pk-icon-hover uk-hidden" title="{{ 'Edit' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: edit()"></a></li>
                                    <li v-if="type.editable !== false && editing[widget.id]"><a class="pk-icon-check pk-icon-hover" title="{{ 'Confirm' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: edit()"></a></li>
                                </ul>
                            </div>

                            <div v-component="{{ component }}" v-with="widget: widget, editing: isEditing"></div>

                        </div>

                    </div>
                </li>
            </ul>

        </div>
    </div>

</div>
