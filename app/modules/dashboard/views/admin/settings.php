<?php $view->script('dashboard-settings', 'app/modules/dashboard/app/settings.js', 'vue-system') ?>

<div id="dashboard" class="uk-form">

   <?php $view->section()->start('toolbar', 'show') ?>

        <div class="uk-button-dropdown" data-uk-dropdown="{ mode: 'click' }">
            <button class="uk-button uk-button-primary" type="button">{{ 'Add Widget' | trans }}</button>
            <div class="uk-dropdown uk-dropdown-small">
                <ul class="uk-nav uk-nav-dropdown">
                    <li v-repeat="type: types">
                        <a href="{{ $url('admin/system/dashboard/add', {type: type.id}) }}">{{ type.name }}</a>
                    </li>
                </ul>
            </div>
        </div>

        <a class="uk-button pk-button-danger" v-show="selected.length" v-on="click: remove">{{ 'Delete' | trans }}</a>

    <?php $view->section()->end() ?>

    <div class="pk-table-fake pk-table-fake-header pk-table-fake-header-indent">
        <div class="pk-table-width-minimum"><input type="checkbox" v-check-all="selected: input[name=id]"></div>
        <div>{{ 'Widget' | trans }}</div>
        <div class="pk-table-width-100">{{ 'Type' | trans }}</div>
    </div>

    <!-- TODO: the classes need to be updated for sortable-->
    <ul class="uk-nestable" data-uk-sortable="{ handleClass: 'uk-nestable-handle', childClass: 'uk-nestable-list-item', placeholderClass: 'uk-nestable-placeholder' }">
        <li v-repeat="widget: widgets" v-ref="ordered">
            <div class="uk-nestable-item pk-table-fake">
                <div class="pk-table-width-minimum"><div class="uk-nestable-handle">​</div></div>
                <div class="pk-table-width-minimum"><input type="checkbox" name="id" value="{{ $key }}"></div>
                <div><a href="{{ $url('admin/system/dashboard/edit', {id: $key}) }}">{{ widget.title }}</a></div>
                <div class="pk-table-width-100">{{ types[widget.type].name }}</div>
            </div>
        </li>
    </ul>

</div>
