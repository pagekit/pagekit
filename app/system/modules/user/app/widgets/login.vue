<template>

    <partial name="settings"></partial>

    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Login Redirect' | trans }}</label>
        <div class="uk-form-controls uk-form-controls-text">
            <input type="text" v-model="widget.settings['redirect.login']">
        </div>
    </div>
    <div class="uk-form-row">
        <label class="uk-form-label">{{ 'Logout Redirect' | trans }}</label>
        <div class="uk-form-controls uk-form-controls-text">
            <input type="text" v-model="widget.settings['redirect.logout']">
        </div>
    </div>


</template>

<script>

    module.exports = {

        section: {
            name: 'site-user-login',
            label: 'Settings',
            active: 'site.user.login',
            priority: 0
        },

        template: __vue_template__,
        props: ['widget', 'config', 'form']

    };

    window.Widgets.component('site-user-login', module.exports)

</script>
