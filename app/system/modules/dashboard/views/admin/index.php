<?php $view->script('dashboard', 'system/dashboard:app/bundle/admin/index.js', ['vue', 'uikit-autocomplete']) ?>

<div id="dashboard">

    <div class="uk-grid pk-grid-small uk-grid-medium" data-uk-grid-margin>
        <div class="uk-width-medium-1-3" v-repeat="column: columns">

            <div class="uk-panel uk-panel-box uk-visible-hover-inline" v-repeat="widget: column">

                <div v-component="widget-panel" inline-template>

                    <div class="uk-position-top-right uk-margin-small-top uk-margin-small-right uk-text-large">
                        <a class="uk-invisible uk-icon-hover uk-icon-remove" title="{{ 'Delete' | trans }}" v-on="click: remove()"></a>
                        <a class="uk-invisible uk-icon-hover uk-icon-pencil" title="{{ 'Edit' | trans }}" v-on="click: edit()" v-if="type.editable !== false"></a>
                    </div>

                    <div v-component="{{ component }}" v-with="widget: widget, editing: isEditing"></div>

                </div>

            </div>

        </div>
    </div>

    <div class="uk-margin">
        <div class="uk-button-dropdown" data-uk-dropdown="{ mode: 'click' }">
            <a class="uk-button" v-on="click: $event.preventDefault()">{{ 'Add Widget' || trans }}</a>
            <div class="uk-dropdown uk-dropdown-small">
                <ul class="uk-nav uk-nav-dropdown">
                    <li v-repeat="type: getTypes()">
                        <a v-on="click: add(type)">{{ type.label }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>
