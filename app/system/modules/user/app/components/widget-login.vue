<template>

    <partial name="settings"></partial>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Login Redirect' | trans }}</label>
        <div class="uk-form-controls uk-form-controls-text">
            <input-link url="{{@ widget.data.redirect_login}}"></input-link>
        </div>
    </div>
    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Logout Redirect' | trans }}</label>
        <div class="uk-form-controls uk-form-controls-text">
            <input-link url="{{@ widget.data.redirect_logout}}"></input-link>
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
