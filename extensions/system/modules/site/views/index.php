<?php

$view->script('site', 'extensions/system/modules/site/app/site.js', ['vue-system', 'vue-validator', 'uikit-nestable']);

?>

<div id="js-site">

    <div class="uk-grid">

        <div class="uk-panel uk-panel-box uk-width-1-4" v-component="menu-list"></div>

        <div class="uk-panel uk-panel-box uk-width-3-4" v-component="node-edit"></div>

    </div>
<!--<pre>{{ $data | json }}</pre>-->
</div>

<script id="menu-list" type="text/template">

    <div class="uk-margin" v-repeat="menu: menus" v-ref="menus">
        <div class="uk-flex">
            <span class="uk-panel-title uk-flex-item-1" v-on="click: edit(menu)">{{ menu.label }}</span>

            <div class="uk-button-dropdown" data-uk-dropdown="{ mode: 'click' }">
                <a v-on="click: $event.preventDefault()"><i class="uk-icon uk-icon-plus"></i></a>
                <div class="uk-dropdown uk-dropdown-small">
                    <ul class="uk-nav uk-nav-dropdown">
                        <li v-repeat="type: types"><a v-on="click: add(menu, type)">{{ type.label }}</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <node-list></node-list>

    </div>

    <p>
        <a v-on="click: edit()"><i class="uk-icon-th-list"></i> {{ 'Create Menu' | trans }}</a>
    </p>

    <div id="modal-menu" class="uk-modal">

        <div class="uk-modal-dialog uk-modal-dialog-slide" v-if="menu">

            <form v-on="valid: save" name="form">

                <p>
                    <input class="uk-width-1-1 uk-form-large" name="label" type="text" v-model="menu.label" placeholder="{{ 'Enter Menu Name' | trans }}" v-valid="alphaNum">
                    <span class="uk-form-help-block uk-text-danger" v-show="form.label.invalid">{{ 'Invalid name.' | trans }}</span>
                </p>
                <p>
                    <input class="uk-width-1-1 uk-form-large" name="id" type="text" v-model="menu.id" placeholder="{{ 'Enter Menu Slug' | trans }}" v-valid="alphaNum, unique">
                    <span class="uk-form-help-block uk-text-danger" v-show="form.id.invalid">{{ 'Invalid slug.' | trans }}</span>
                </p>

                <button class="uk-button uk-button-primary" v-attr="disabled: form.invalid">{{ 'Save' | trans }}</button>
                <button class="uk-button uk-modal-close" v-on="click: cancel">{{ 'Cancel' | trans }}</button>
                <button v-show="menu.oldId" class="uk-button uk-button-danger uk-float-right" v-on="click: delete">{{ 'Delete' | trans }}</button>

            </form>

        </div>

    </div>

</script>

<script id="node-list" type="text/template">

    <ul v-class="uk-nestable: !node, uk-nestable-list: node">
        <li class="uk-nestable-list-item" v-class="uk-parent: hasChildren(node), uk-active: current == node" v-repeat="node: children" data-id="{{ node.id }}">

            <div class="uk-nestable-item uk-visible-hover-inline" v-on="click: select(node)">
                <div class="uk-nestable-handle"></div>
                <div data-nestable-action="toggle"></div>
                {{ node.title }}

                <a class="uk-hidden uk-float-right" v-on="click: delete" title="{{ 'Delete' | trans }}"><i class="uk-icon-minus-circle"></i></a>
            </div>

            <node-list v-if="hasChildren(node)"></node-list>

        </li>
    </ul>

</script>

<script id="node-edit" type="text/template">

    <form class="uk-form uk-form-stacked" v-on="submit: save">

        <div class="uk-clearfix uk-margin">

            <div class="uk-float-left">

                <h2 v-if="node.id" class="uk-h2">{{ node.title }} ({{ type }})</h2>
                <h2 v-if="!node.id" class="uk-h2">{{ 'Add %type%' | trans {type:type} }}</h2>

            </div>

            <div class="uk-float-right">

                <a class="uk-button" v-on="click: reset()">{{ 'Cancel' | trans }}</a>
                <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

            </div>

        </div>

        <div class="uk-form-row">
            <label for="form-title" class="uk-form-label">{{ 'Title' | trans }}</label>
            <div class="uk-form-controls">
                <input id="form-title" class="uk-form-width-large" type="text" v-model="node.title" required>
            </div>
        </div>

        <div class="uk-form-row">
            <label for="form-slug" class="uk-form-label">{{ 'Slug' | trans }}</label>
            <div class="uk-form-controls">
                <span>{{ getPath() }}</span><br>
                <input id="form-slug" class="uk-form-width-large" type="text" v-model="node.slug">
            </div>
        </div>

    </form>

</script>
