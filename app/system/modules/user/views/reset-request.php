<?php $view->script('reset', 'system/user:app/bundle/reset.js', ['vue']) ?>

<form id="user-reset" class="pk-user pk-user-reset uk-form uk-form-stacked uk-width-medium-1-2 uk-width-large-1-3 uk-container-center" v-validator="form" @submit.prevent="submit | valid" v-cloak>

    <h1 class="uk-h2 uk-text-center"><?= __('Forgot Password') ?></h1>

    <div class="uk-alert" :class="{'uk-alert-danger': !success, 'uk-alert-success': success}" v-show="message">
        {{ message }}
    </div>

    <div v-else>
        <p><?= __('Please enter your email address. You will receive a link to create a new password via email.') ?></p>

        <div class="uk-form-row">
            <input class="uk-width-1-1" type="text" name="email" placeholder="<?= __('Email') ?>" autofocus v-model="email" v-validate:required v-validate:email lazy>
            <p class="uk-form-help-block uk-text-danger" v-show="form.email.invalid">{{ 'Field must be a valid email address.' | trans }}</p>
        </div>

        <p class="uk-form-row">
            <button class="uk-button uk-button-primary uk-button-large uk-width-1-1" type="submit"><?= __('Request password') ?></button>
        </p>
    </div>

</form>
