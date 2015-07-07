<template>

    <div class="uk-form-row">
        <label for="form-link-page" class="uk-form-label">{{ 'Page' | trans }}</label>

        <div class="uk-form-controls">
            <select id="form-link-page" class="uk-form-width-large" v-model="view">
                <option value="">{{ '- Select View -' | trans}}</option>
                <option value="@user/login">{{ 'User Login' | trans}}</option>
                <option value="@user/logout">{{ 'User Logout' | trans}}</option>
                <option value="@user/registration">{{ 'User Registration' | trans}}</option>
                <option value="@user/profile">{{ 'User Profile' | trans}}</option>
                <option value="@user/resetpassword">{{ 'User Password Reset' | trans}}</option>
            </select>
        </div>
    </div>

</template>

<script>

    module.exports = {

        link: {
            name: 'user',
            label: 'User'
        },

        props: ['url'],

        data: function () {
            return {
                view: ''
            }
        },

        watch: {

            url: {
                handler: function () {
                    this.view = '';
                },
                immediate: true
            },

            view: function (view) {
                this.url = view;
            }

        },

        template: __vue_template__

    };

    window.Linkpicker.component('user', module.exports);

</script>
