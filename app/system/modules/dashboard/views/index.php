<?php $view->script('dashboard', 'system/dashboard:app/bundle/index.js', ['vue', 'uikit-autocomplete']) ?>

<div id="dashboard" v-cloak>

    <div class="uk-alert uk-alert-info" v-show="hasUpdate"><a href="admin/system/update">{{ 'Pagekit %version% is available! Please update now.' | trans update }}</a></div>

    <div class="uk-margin uk-flex uk-flex-right" data-uk-margin>
        <div class="uk-button-dropdown" data-uk-dropdown="{ mode: 'click' }">
            <a class="uk-button uk-button-primary" v-on="click: $event.preventDefault()">{{ 'Add Widget' || trans }}</a>
            <div class="uk-dropdown uk-dropdown-small uk-dropdown-flip">
                <ul class="uk-nav uk-nav-dropdown">
                    <li v-repeat="type: getTypes()">
                        <a class="uk-dropdown-close" v-on="click: add(type)">{{ type.label }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="uk-grid uk-grid-medium uk-grid-match" data-uk-grid-margin>
        <div class="uk-width-medium-1-3" v-repeat="[0,1,2]">

            <ul class="uk-sortable pk-sortable" data-column="{{ $index }}">
                <li v-repeat="widget: widgets | column $index" data-id="{{ widget.id }}" data-idx="{{ widget.idx }}">
                    <panel class="uk-panel uk-panel-box uk-visible-hover-inline"></panel>
                </li>
                <li class="uk-hidden" v-if="false">Vue fix for empty lists<li>
            </ul>

        </div>

    </div>

</div>
