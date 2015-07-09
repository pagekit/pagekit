<template>

    <div class="uk-grid pk-grid-large" data-uk-grid-margin>
        <div class="uk-flex-item-1 uk-form-horizontal">

            <div class="uk-form-row">
                <label for="form-title" class="uk-form-label">{{ 'Title' | trans }}</label>
                <div class="uk-form-controls">
                    <input id="form-title" class="uk-form-width-large" type="text" name="title" v-model="widget.title" v-valid="required">
                    <p class="uk-form-help-block uk-text-danger" v-show="form.title.invalid">{{ 'Title cannot be blank.' | trans }}</p>
                </div>
            </div>

            <div class="uk-form-row">
                <label class="uk-form-label">{{ 'Login Redirect' | trans }}</label>
                <div class="uk-form-controls">
                    <input-link url="{{@ widget.data.redirect_login}}"></input-link>
                </div>
            </div>
            <div class="uk-form-row">
                <label class="uk-form-label">{{ 'Logout Redirect' | trans }}</label>
                <div class="uk-form-controls">
                    <input-link url="{{@ widget.data.redirect_logout}}"></input-link>
                </div>
            </div>

        </div>
        <div class="pk-width-sidebar pk-width-sidebar-large">

            <partial name="settings"></partial>

        </div>
    </div>

</template>

<script>

    module.exports = {

        section: {
            name: 'widget-login',
            label: 'Settings',
            active: 'system/widget-login',
            priority: 0
        },

        replace: false,

        props: ['widget', 'config', 'form'],

        template: __vue_template__

    };

    window.Widgets.component('widget-login', module.exports);

</script>
