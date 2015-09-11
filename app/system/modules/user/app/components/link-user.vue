<template>

    <div class="uk-form-row">
        <label for="form-link-user" class="uk-form-label">{{ 'View' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-link-user" class="uk-width-1-1" v-model="link">
                <option value="@user/login">{{ 'User Login' | trans }}</option>
                <option value="@user/logout">{{ 'User Logout' | trans }}</option>
                <option value="@user/registration">{{ 'User Registration' | trans }}</option>
                <option value="@user/profile">{{ 'User Profile' | trans }}</option>
                <option value="@user/resetpassword">{{ 'User Password Reset' | trans }}</option>
            </select>
        </div>
    </div>

</template>

<script>

    module.exports = {

        link: {
            label: 'User'
        },

        props: ['link'],

        ready: function () {
            this.$set('link', '@user/login');
        }

    };

    window.Links.components['link-user'] = module.exports;

</script>
