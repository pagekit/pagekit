<?php $view->script('profile', 'system/user:app/bundle/profile.js', ['vue', 'uikit-form-password']) ?>

<form id="user-profile" class="pk-user pk-user-profile uk-form uk-form-stacked uk-width-medium-1-2 uk-width-large-1-3 uk-container-center" v-validator="form" @submit.prevent="save | valid">

    <h1 class="uk-h2 uk-text-center">{{ 'Your Profile' | trans }}</h1>

    <div class="uk-form-row">
        <input class="uk-width-1-1" type="text" name="name" placeholder="<?= __('Name') ?>" v-model="user.name" v-validate:required>
        <p class="uk-form-help-block uk-text-danger" v-show="form.name.invalid">{{ 'Name cannot be blank.' | trans }}</p>
    </div>

    <div class="uk-form-row">
        <input class="uk-width-1-1" type="text" name="email" placeholder="<?= __('Email') ?>" v-model="user.email" v-validate:email v-validate:required>
        <p class="uk-form-help-block uk-text-danger" v-show="form.email.invalid">{{ 'Invalid Email.' | trans }}</p>
    </div>

    <div class="uk-form-row">
        <a href="#" data-uk-toggle="{ target: '.js-password' }">{{ 'Change password' | trans }}</a>
    </div>

    <div class="uk-form-row js-password uk-hidden">
        <div class="uk-form-password uk-width-1-1">
            <input class="uk-width-1-1" type="password" value="" placeholder="<?= __('Current Password') ?>" v-model="user.password_old">
            <a href="" class="uk-form-password-toggle" tabindex="-1" data-uk-form-password="{ lblShow: '{{ 'Show' | trans }}', lblHide: '{{ 'Hide' | trans }}' }">{{ 'Show' | trans }}</a>
        </div>
    </div>

    <div class="uk-form-row js-password uk-hidden">
        <div class="uk-form-password uk-width-1-1">
            <input class="uk-width-1-1" type="password" value="" placeholder="<?= __('New Password') ?>" v-model="user.password_new">
            <a href="" class="uk-form-password-toggle" tabindex="-1" data-uk-form-password="{ lblShow: '{{ 'Show' | trans }}', lblHide: '{{ 'Hide' | trans }}' }">{{ 'Show' | trans }}</a>
        </div>
    </div>

    <p class="uk-form-row">
        <button class="uk-button uk-button-primary uk-button-large uk-width-1-1" type="submit">{{ 'Save' | trans }}</button>
    </p>

</form>
