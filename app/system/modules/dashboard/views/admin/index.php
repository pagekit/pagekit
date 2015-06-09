<?php $view->script('dashboard', 'system/dashboard:app/bundle/admin/index.js', ['vue', 'uikit-autocomplete']) ?>

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

    <div class="uk-grid pk-grid-small uk-grid-medium" data-uk-grid-margin>
        <div class="uk-width-medium-1-3" v-repeat="column: columns">

            <div class="uk-panel uk-panel-box uk-visible-hover-inline" v-repeat="widget: column">

                <div v-component="widget-panel" inline-template>

                    <div class="uk-panel-badge">
                        <a class="uk-icon-remove uk-icon-hover uk-hidden" title="{{ 'Delete' | trans }}" v-on="click: remove()"></a>
                        <a class="uk-icon-cog uk-icon-hover uk-hidden" title="{{ 'Settings' | trans }}" v-on="click: edit()" v-if="type.editable !== false"></a>
                    </div>

                    <div v-component="{{ component }}" v-with="widget: widget, editing: isEditing"></div>

                </div>

            </div>

        </div>
    </div>

</div>
