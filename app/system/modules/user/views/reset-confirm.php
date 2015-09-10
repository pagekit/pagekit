<?php $view->script('uikit-form-password') ?>

<form class="uk-article uk-form uk-form-stacked" action="<?= $view->url('@user/resetpassword/confirm', ['user' => $username, 'key' => $activation]) ?>" method="post">

    <h1 class="uk-article-title"><?= __('Password Confirmation') ?></h1>

    <div class="uk-form-row js-password">
        <label for="form-password" class="uk-form-label"><?= __('New Password') ?></label>
        <div class="uk-form-controls">
            <div class="uk-form-password">
                <input id="form-password" class="uk-form-width-medium" type="password" name="password" value="">
                <a href="" class="uk-form-password-toggle" tabindex="-1" data-uk-form-password="{ lblShow: '<?= __('Show') ?>', lblHide: '<?= __('Hide') ?>' }"><?= __('Show') ?></a>
            </div>
        </div>
    </div>

    <p class="uk-form-row">
        <button class="uk-button uk-button-primary" type="submit"><?= __('Submit') ?></button>
    </p>

    <?php $view->token()->get() ?>

</form>
