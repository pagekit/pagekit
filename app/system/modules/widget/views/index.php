<?php $view->script('widget-index', 'system/widget:app/bundle/index.js', ['widgets', 'uikit-sortable']) ?>

<style media="screen">
    .uk-sortable {
        min-height: 50px;
    }
</style>

<div id="widgets" class="uk-form" v-cloak>

    <div class="uk-grid pk-grid-large" data-uk-grid-margin>
        <div class="pk-width-sidebar">

            <div class="uk-panel">

                <ul class="uk-nav uk-nav-side">
                    <li v-class="uk-active: active()">
                        <a v-on="click: select()">{{ 'All' | trans }}</a>
                    </li>
                    <li v-class="uk-active: active(unassigned)" v-show="unassigned.widgets.length">
                        <a v-on="click: select(unassigned)">{{ 'Unassigned' | trans }} <span class="uk-text-muted uk-float-right">{{ unassigned.widgets.length }}</span></a>
                    </li>
                    <li class="uk-nav-header">{{ 'Positions' | trans }}</li>
                    <li v-class="uk-active: active(pos)" v-repeat="pos: config.positions" v-var="pos.widgets: pos.assigned | assigned">
                        <a v-on="click: select(pos)">{{ pos.label }}  <span class="uk-text-muted uk-float-right" v-show="pos.widgets.length">{{ pos.widgets.length }}</span></a>
                    </li>
                </ul>

            </div>

        </div>
        <div class="uk-flex-item-1">

            <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
                <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

                    <h2 class="uk-margin-remove" v-show="!selected.length">{{ position ? position.label : 'All' | trans }}</h2>
                    <h2 class="uk-margin-remove" v-show="selected.length">{{ '{1} %count% Widget selected|]1,Inf[ %count% Widgets selected' | transChoice selected.length {count:selected.length} }}</h2>

                    <div class="uk-margin-left" v-show="selected.length">
                        <ul class="uk-subnav pk-subnav-icon">
                            <li><a class="pk-icon-check pk-icon-hover" title="{{ 'Publish' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: status(1)"></a></li>
                            <li><a class="pk-icon-block pk-icon-hover" title="{{ 'Unpublish' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: status(0)"></a></li>
                            <li><a class="pk-icon-copy pk-icon-hover" title="{{ 'Copy' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: copy()"></a></li>
                            <li data-uk-dropdown="{mode: 'click'}">
                                <a class="pk-icon-move pk-icon-hover" title="{{ 'Move' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: $event.preventDefault()"></a>
                                <div class="uk-dropdown uk-dropdown-small">
                                    <ul class="uk-nav uk-nav-dropdown">
                                        <li v-repeat="config.positions" track-by="name"><a v-on="click: move(name, selected)">{{ label }}</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: remove"></a></li>
                        </ul>
                    </div>

                    <div class="pk-search">
                        <div class="uk-search">
                            <input class="uk-search-field" type="text" v-model="config.filter.search" debounce="300">
                        </div>
                    </div>

                </div>
                <div class="uk-position-relative" data-uk-margin>

                    <div data-uk-dropdown="{mode: 'click'}">
                        <button class="uk-button uk-button-primary" type="button">{{ 'Add Widget' | trans }}</button>
                        <div class="uk-dropdown uk-dropdown-small uk-dropdown-flip">
                            <ul class="uk-nav uk-nav-dropdown">
                                <li v-repeat="type: types"><a href="{{ $url.route('admin/site/widget/edit', {type: type.name, position:(position ? position.name:'')}) }}">{{ type.label || type.name }}</a></li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>

            <div class="uk-overflow-container">

                <div class="pk-table-fake pk-table-fake-header pk-table-fake-border" v-show="!position && emptyafterfilter()">
                    <div class="pk-table-width-minimum"><input type="checkbox" v-check-all="selected: input[name=id]" number></div>
                    <div class="pk-table-min-width-100">{{ position ? 'Title' : pos.label | trans }}</div>
                    <div class="pk-table-width-100 uk-text-center">{{ 'Status' | trans }}</div>
                    <div class="pk-table-width-150">{{ 'Type' | trans }}</div>
                    <div class="pk-table-width-100">
                        <input-filter title="{{ 'Pages' | trans }}" value="{{@ config.filter.node}}" options="{{ nodes }}" number></input-filter>
                    </div>
                </div>

                <h3 class="uk-h1 uk-text-muted uk-text-center uk-margin-bottom" v-show="!position && emptyafterfilter()">{{ 'No widgets found.' | trans }}</h3>


                <div class="uk-margin-bottom" v-repeat="pos: positions" track-by="name" v-show="pos | show">

                    <div class="pk-table-fake pk-table-fake-header" v-class="pk-table-fake-border: (!pos.widgets.length || (position && emptyafterfilter(pos.widgets)))" v-show="position || (!position && !emptyafterfilter(pos.widgets))">
                        <div class="pk-table-width-minimum"><input type="checkbox" v-check-all="selected: input[name=id]" number></div>
                        <div class="pk-table-min-width-100">{{ position ? 'Title' : pos.label | trans }}</div>
                        <div class="pk-table-width-100 uk-text-center">{{ 'Status' | trans }}</div>
                        <div class="pk-table-width-150">{{ 'Type' | trans }}</div>
                        <div class="pk-table-width-100">
                            <input-filter title="{{ 'Pages' | trans }}" value="{{@ config.filter.node}}" options="{{ nodes }}" number></input-filter>
                        </div>
                    </div>

                    <h3 class="uk-h1 uk-text-muted uk-text-center" v-show="!pos.widgets.length || (position && emptyafterfilter(pos.widgets))">{{ 'No widgets found.' | trans }}</h3>

                    <ul class="uk-sortable uk-list uk-margin-remove" v-component="position" v-show="!emptyafterfilter(pos.widgets)" data-position="{{ pos.name }}" inline-template>
                        <li v-class="uk-active: isSelected(widget.id)" v-repeat="widget: pos.widgets" v-var="type: widget | type" data-id="{{ widget.id }}" v-show="infilter(widget)">

                            <div class="uk-nestable-panel pk-table-fake uk-form">
                                <div class="pk-table-width-minimum"><input type="checkbox" name="id" value="{{ widget.id }}"></div>
                                <div class="pk-table-min-width-100">
                                    <a href="{{ $url.route('admin/site/widget/edit', {id: widget.id}) }}" v-if="type">{{ widget.title }}</a>
                                    <span v-if="!type">{{ widget.title }}</span>
                                </div>
                                <div class="pk-table-width-100 uk-text-center">
                                    <td class="uk-text-center">
                                        <a v-class="pk-icon-circle-danger: !widget.status, pk-icon-circle-success: widget.status" v-on="click: toggleStatus(widget)"></a>
                                    </td>
                                </div>
                                <div class="pk-table-width-150">{{ type.label || type.name }}</div>
                                <div class="pk-table-width-100">
                                    {{ getPageFilter(widget) }}
                                </div>
                            </div>

                        </li>
                    </ul>

                </div>

            </div>

        </div>
    </div>

</div>
