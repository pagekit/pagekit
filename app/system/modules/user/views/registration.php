<?php $view->script('uikit-form-password') ?>

<form class="uk-article uk-form uk-form-stacked" action="<?= $view->url('@user/registration/register') ?>" method="post">

    <h1 class="uk-article-title"><?= __('Registration') ?></h1>

    <div class="uk-form-row">
        <label for="form-username" class="uk-form-label"><?= __('Username') ?></label>
        <div class="uk-form-controls">
            <input id="form-username" class="uk-form-width-large" type="text" name="user[username]" value="" required>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-name" class="uk-form-label"><?= __('Name') ?></label>
        <div class="uk-form-controls">
            <input id="form-name" class="uk-form-width-large" type="text" name="user[name]" value="" required>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-email" class="uk-form-label"><?= __('Email') ?></label>
        <div class="uk-form-controls">
            <input id="form-email" class="uk-form-width-large" type="email" name="user[email]" value="" required>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-password" class="uk-form-label"><?= __('Password') ?></label>
        <div class="uk-form-controls">
            <div class="uk-form-password">
                <input id="form-password" class="uk-form-width-large" type="password" name="user[password]" value="">
                <a href="" class="uk-form-password-toggle" data-uk-form-password="{ lblShow: '<?= __('Show') ?>', lblHide: '<?= __('Hide') ?>' }"><?= __('Show') ?></a>
            </div>
        </div>
    </div>

    <div class="uk-form-row">
        <button class="uk-button uk-button-primary" type="submit"><?= __('Submit') ?></button>
    </div>

    <?php $view->token()->get() ?>

</form>
