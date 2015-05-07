<?php $view->script('registration', 'system/user:app/registration.js', ['system', 'vue-validator', 'uikit-form-password']) ?>

<form id="user-registration" class="uk-article uk-form uk-form-stacked" name="form" v-on="valid: submit">

    <h1 class="uk-article-title"><?= __('Registration') ?></h1>

    <div class="uk-alert uk-alert-danger" v-show="error">{{ error }}</div>

    <div class="uk-form-row">
        <label for="form-username" class="uk-form-label"><?= __('Username') ?></label>
        <div class="uk-form-controls">
            <input id="form-username" class="uk-form-width-large" type="text" name="username" v-model="user.username" v-valid="required">
            <p class="uk-form-help-block uk-text-danger" v-show="form.username.invalid"><?= __('Username cannot be blank.') ?></p>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-name" class="uk-form-label"><?= __('Name') ?></label>
        <div class="uk-form-controls">
            <input id="form-name" class="uk-form-width-large" type="text" name="name" v-model="user.name" v-valid="required">
            <p class="uk-form-help-block uk-text-danger" v-show="form.name.invalid"><?= __('Name cannot be blank.') ?></p>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-email" class="uk-form-label"><?= __('Email') ?></label>
        <div class="uk-form-controls">
            <input id="form-email" class="uk-form-width-large" type="email" name="email" v-model="user.email" v-valid="email">
            <p class="uk-form-help-block uk-text-danger" v-show="form.email.invalid"><?= __('Email address is invalid.') ?></p>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-password" class="uk-form-label"><?= __('Password') ?></label>
        <div class="uk-form-controls">
            <div class="uk-form-password">
                <input id="form-password" class="uk-form-width-large" type="password" name="password" v-model="user.password" v-valid="required">
                <a href="" class="uk-form-password-toggle" data-uk-form-password="{ lblShow: '<?= __('Show') ?>', lblHide: '<?= __('Hide') ?>' }"><?= __('Show') ?></a>
                <p class="uk-form-help-block uk-text-danger" v-show="form.password.invalid"><?= __('Password cannot be blank.') ?></p>
            </div>
        </div>
    </div>

    <div class="uk-form-row">
        <button class="uk-button uk-button-primary" type="submit"><?= __('Submit') ?></button>
    </div>

</form>
