<?php $view->script('user-edit', 'system/user:app/bundle/user-edit.js', ['vue', 'uikit-form-password']) ?>

<form id="user-edit" class="uk-form uk-form-horizontal" v-validator="form" v-on="submit: save | valid" v-cloak>

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <h2 class="uk-margin-remove" v-if="user.id">{{ 'Edit User' | trans }}</h2>
            <h2 class="uk-margin-remove" v-if="!user.id">{{ 'Add User' | trans }}</h2>

        </div>
        <div data-uk-margin>

            <a class="uk-button uk-margin-small-right" v-attr="href: $url.route('admin/user')">{{ user.id ? 'Close' : 'Cancel' | trans }}</a>
            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

        </div>
    </div>

    <ul class="uk-tab" v-el="tab" v-show="sections.length > 1">
        <li v-repeat="section: sections"><a>{{ section.label | trans }}</a></li>
    </ul>

    <div class="uk-switcher uk-margin" v-el="content">
        <div v-repeat="section: sections">
            <component is="{{ section.name }}"></component>
        </div>
    </div>

</form>
