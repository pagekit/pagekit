<?php $view->script('widget-index', 'widget:app/bundle/index.js', ['widgets', 'uikit-sortable']) ?>

<style media="screen">
    .uk-sortable {
        min-height: 50px;
    }
</style>

<div id="widget-index" v-cloak>

    <div class="uk-grid pk-grid-large" data-uk-grid-margin>
        <div class="pk-width-sidebar">

            <div class="uk-panel">

                <ul class="uk-nav uk-nav-side">
                    <li class="uk-visible-hover" v-class="uk-active: active()">
                        <a v-on="click: select()">{{ 'All' | trans }}</a>
                    </li>
                    <li class="uk-nav-header">Positions</li>
                    <li class="uk-visible-hover" v-class="uk-active: active(p)" v-repeat="p: config.positions" track-by="name">
                        <a v-on="click: select(p)">{{ p.label }}</a>
                    </li>
                </ul>

            </div>

        </div>
        <div class="uk-flex-item-1">

            <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
                <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

                    <h2 class="uk-margin-remove">{{ position ? position.label : 'All' | trans }}</h2>

                    <div class="uk-margin-left" v-show="selected.length">
                        <ul class="uk-subnav pk-subnav-icon">
                            <li><a class="pk-icon-check pk-icon-hover" title="{{ 'Publish' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: status(1)"></a></li>
                            <li data-uk-dropdown="{mode: 'click'}">
                                <a class="pk-icon-move pk-icon-hover" title="{{ 'Move' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: $event.preventDefault()"></a>
                                <div class="uk-dropdown uk-dropdown-small">
                                    <ul class="uk-nav uk-nav-dropdown">
                                        <li v-repeat="config.positions"><a v-on="click: moveNodes(id)">{{ label }}</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}" data-uk-tooltip="{delay: 500}" v-on="click: remove"></a></li>
                        </ul>
                    </div>

                </div>
                <div class="uk-position-relative" data-uk-margin>

                    <div data-uk-dropdown="{mode: 'click'}">
                        <button class="uk-button uk-button-primary" type="button">{{ 'Add Widget' | trans }}</button>
                        <div class="uk-dropdown uk-dropdown-small uk-dropdown-flip">
                            <ul class="uk-nav uk-nav-dropdown">
                                <li v-repeat="type: config.types"><a href="{{ $url('admin/widget/edit', {type: type.name}) }}">{{ type.name }}</a></li>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>

            <div class="uk-overflow-container">

                <div class="pk-table-fake pk-table-fake-header pk-table-fake-border">
                    <div class="pk-table-width-minimum"><input type="checkbox" v-check-all="selected: input[name=id]"></div>
                    <div class="pk-table-min-width-100">{{ 'Title' | trans }}</div>
                    <div class="pk-table-width-150">{{ 'Type' | trans }}</div>
                </div>

                <div v-repeat="p: config.positions" track-by="name" v-show="p | show">

                    <div class="pk-table-fake pk-table-fake-header pk-table-fake-subheading" v-show="!position">
                        <div>
                            {{ p.label | trans }}
                            <span class="uk-text-muted" v-if="p.description">{{ p.description | trans }}</span>
                        </div>
                    </div>

                    <ul class="uk-sortable uk-list uk-form" data-uk-sortable="{group:'position', removeWhitespace:false}" data-position="{{ p.name }}">
                        <li v-repeat="id: p.assigned | exists" data-id="{{ id }}">
                            <v-item class="uk-nestable-panel pk-table-fake uk-form" widget="{{ widgets[id] }}" inline-template>
                                <div class="pk-table-width-minimum">
                                    <input type="checkbox" name="id" value="{{ widget.id }}">
                                </div>
                                <div class="pk-table-min-width-100">
                                    <a href="{{ $url('admin/widget/edit', {id: widget.id}) }}" v-if="type">{{ widget.title }}</a>
                                    <span v-if="!type">{{ widget.title }}</span>
                                </div>
                                <div class="pk-table-width-150">{{ type.name }}</div>
                            </v-item>
                        </li>
                    </ul>

                </div>

            </div>

        </div>
    </div>

</div>
