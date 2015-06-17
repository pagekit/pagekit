<?php $view->script('widget-index', 'widget:app/bundle/index.js', 'widgets') ?>

<div id="widget-index" v-cloak>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

            <h2 class="uk-margin-remove" v-show="!selected.length">{{ '{0} %count% Widgets|{1} %count% Widget|]1,Inf[ %count% Widgets' | transChoice count {count:count} }}</h2>
            <h2 class="uk-margin-remove" v-show="selected.length">{{ '{1} %count% Widget selected|]1,Inf[ %count% Widgets selected' | transChoice selected.length {count:selected.length} }}</h2>

            <div class="uk-margin-left" v-show="selected.length">
                <ul class="uk-subnav pk-subnav-icon">
                    <li><a class="pk-icon-delete pk-icon-hover" title="Delete" data-uk-tooltip="{delay: 500}" v-on="click: remove"></a></li>
                    <li><a class="uk-icon-check-circle-o" title="Activate" data-uk-tooltip="{delay: 500}" v-on="click: status(1)"></a></li>
                </ul>
            </div>

            <div class="pk-search">
                <div class="uk-search">
                    <input class="uk-search-field" type="text" v-model="config.filter.search" debounce="300">
                </div>
            </div>

        </div>
        <div data-uk-margin>

            <div class="uk-button-dropdown" data-uk-dropdown="{ mode: 'click' }">
                <button class="uk-button uk-button-primary" type="button">{{ 'Add Widget' | trans }}</button>
                <div class="uk-dropdown uk-dropdown-small">
                    <ul class="uk-nav uk-nav-dropdown">
                        <li v-repeat="type: config.types">
                            <a href="{{ $url('admin/widget/edit', {type: type.id}) }}">{{ type.name }}</a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    <div class="uk-overflow-container">

        <div class="pk-table-fake pk-table-fake-header pk-table-fake-border">
            <div class="pk-table-width-minimum"><input type="checkbox"  v-check-all="selected: input[name=id]"></div>
            <div class="pk-table-min-width-100">{{ 'Title' | trans }}</div>
            <div class="pk-table-width-150">{{ 'Position' | trans }}</div>
            <div class="pk-table-width-150">{{ 'Type' | trans }}</div>
        </div>

        <div v-repeat="position: config.positions" v-show="hasWidgets(position)">

            <div class="pk-table-fake pk-table-fake-header pk-table-fake-subheading">
                <div>
                    {{ position.name | trans }}
                    <span class="uk-text-muted" v-if="position.description">{{ position.description | trans }}</span>
                </div>
            </div>

            <ul class="uk-nestable uk-form" v-el="nestable">
                <li class="uk-nestable-item" data-id="{{ widget.id }}" v-repeat="widget: positions[position.id]">

                    <div class="uk-nestable-panel pk-table-fake">
                        <div class="pk-table-width-minimum">
                            <input type="checkbox" name="id" value="{{ widget.id }}">
                        </div>
                        <div class="pk-table-min-width-100">
                            <a href="{{ $url('admin/widget/edit', {id: widget.id}) }}" v-if="getType(widget)">{{ widget.title }}</a>
                            <span v-if="!getType(widget)">{{ widget.title }}</span>
                        </div>
                        <div class="pk-table-width-150">
                            <div class="uk-form-select uk-nestable-nodrag" v-el="select">
                                <a></a>
                                <select class="uk-width-1-1" v-model="position.id" v-on="input: reassign" options="positionOptions"></select>
                            </div>
                        </div>
                        <div class="pk-table-width-150">{{ getType(widget).name }}</div>
                    </div>

                </li>
            </ul>

        </div>

    </div>

</div>