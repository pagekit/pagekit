<div id="js-site">

    <div class="uk-grid">

        <div class="uk-panel uk-panel-box uk-width-1-4" v-component="menu-list" inline-template>

            <!-- Remark: v-transition is added to force a complete refresh after the nodes have been updated -->
            <div class="uk-margin" v-repeat="menu: menus" v-transition="">
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

                <ul class="uk-nestable" v-el="nestable" v-component="node-list"></ul>

            </div>

            <p>
                <a v-on="click: edit()"><i class="uk-icon-th-list"></i> {{ 'Create Menu' | trans }}</a>
            </p>

            <div v-el="modal" class="uk-modal" v-partial="#modal-menu"></div>

        </div>

        <div class="uk-panel uk-panel-box uk-width-3-4" v-component="node-edit" inline-template>

            <form class="uk-form uk-form-horizontal" name="form" v-on="valid: save">

                <div class="uk-clearfix uk-margin">

                    <div class="uk-float-left">

                        <h2 v-if="node.id" class="uk-h2">{{ node.title }} ({{ type.label }})</h2>
                        <h2 v-if="!node.id" class="uk-h2">{{ 'Add %type%' | trans {type:type.label} }}</h2>

                    </div>

                    <div class="uk-float-right">

                        <a class="uk-button" v-on="click: cancel()">{{ 'Cancel' | trans }}</a>
                        <button class="uk-button uk-button-primary" type="submit" v-attr="disabled: form.invalid">{{ 'Save' | trans }}</button>

                    </div>

                </div>

                <div v-el="edit"></div>

                <pre>{{ node | json }}</pre>

            </form>

        </div>

    </div>

</div>

<script id="node-item" type="text/template">

    <li class="uk-nestable-list-item" v-class="uk-parent: isParent, uk-active: isActive" data-id="{{ node.id }}">

        <div class="uk-nestable-item uk-visible-hover-inline" v-on="click: select(node)">
            <div class="uk-nestable-handle"></div>
            <div data-nestable-action="toggle"></div>
            {{ node.title }}

            <a class="uk-hidden uk-float-right" v-on="click: delete" title="{{ 'Delete' | trans }}"><i class="uk-icon-minus-circle"></i></a>
        </div>

        <ul v-if="isParent" class="uk-nestable-list" v-component="node-list"></ul>

    </li>

</script>

<script id="modal-menu" type="text/template">

    <div class="uk-modal-dialog uk-modal-dialog-slide">

        <form v-on="valid: save" name="menuform">

            <p>
                <input class="uk-width-1-1 uk-form-large" name="label" type="text" v-model="menu.label" placeholder="{{ 'Enter Menu Name' | trans }}" v-valid="alphaNum">
                <span class="uk-form-help-block uk-text-danger" v-show="menuform.label.invalid">{{ 'Invalid name.' | trans }}</span>
            </p>
            <p>
                <input class="uk-width-1-1 uk-form-large" name="id" type="text" v-model="menu.id" placeholder="{{ 'Enter Menu Slug' | trans }}" v-valid="alphaNum, unique">
                <span class="uk-form-help-block uk-text-danger" v-show="menuform.id.invalid">{{ 'Invalid slug.' | trans }}</span>
            </p>

            <button class="uk-button uk-button-primary" v-attr="disabled: menuform.invalid">{{ 'Save' | trans }}</button>
            <button class="uk-button uk-modal-close" v-on="click: cancel">{{ 'Cancel' | trans }}</button>
            <button v-show="menu.oldId" class="uk-button uk-button-danger uk-float-right" v-on="click: delete">{{ 'Delete' | trans }}</button>

        </form>
    </div>

</script>
