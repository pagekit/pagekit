<?php $view->script('dashboard', 'system/dashboard:app/bundle/admin/index.js', ['system', 'uikit-autocomplete']) ?>

<div id="dashboard">

    <div class="uk-clearfix">

        <div class="uk-button-dropdown uk-float-right" data-uk-dropdown="{ mode: 'click' }">
            <a v-on="click: $event.preventDefault()"><i class="uk-icon uk-icon-medium uk-icon-plus"></i></a>
            <div class="uk-dropdown uk-dropdown-small">
                <ul class="uk-nav uk-nav-dropdown">
                    <li v-repeat="type: getTypes()">
                        <a v-on="click: add(type)">{{ type.label }}</a>
                    </li>
                </ul>
            </div>
        </div>

    </div>

    <div class="uk-grid pk-grid-small uk-grid-medium" data-uk-grid-margin>

        <div v-repeat="column: columns" class="uk-width-medium-1-3">

            <div v-repeat="widget: column" class="uk-panel uk-panel-box">

                <div v-component="widget-panel" inline-template>
                    <span class="uk-position-top-right " style="top: 15px; right: 15px;">
                        <a class="uk-icon-hover uk-icon-small uk-icon-remove" title="{{ 'Delete' | trans }}" v-on="click: remove()"></a>
                        <a class="uk-icon-hover uk-icon-small uk-icon-edit" title="{{ 'Edit' | trans }}" v-on="click: edit()" v-if="type.editable !== false"></a>
                    </span>

                    <div v-component="{{ component }}" v-with="widget: widget, editing: isEditing"></div>
                </div>

            </div>

        </div>

    </div>

</div>
