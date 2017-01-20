<?php $view->script('uikit-form-password') ?>

<form class="pk-user pk-user-reset uk-form uk-form-stacked uk-width-medium-1-2 uk-width-large-1-3 uk-container-center" action="<?= $view->url('@user/resetpassword/confirm', ['key' => $activation]) ?>" method="post">

    <?php if($error): ?>
    <div class="uk-alert uk-alert-danger">
        <?= $error; ?>
    </div>
    <?php endif; ?>

    <h1 class="uk-h2 uk-text-center"><?= __('Password Confirmation') ?></h1>

    <div class="uk-form-row js-password">
        <div class="uk-form-password uk-width-1-1">
            <input class="uk-width-1-1" type="password" name="password" value="" placeholder="<?= __('New Password') ?>">
            <a href="" class="uk-form-password-toggle" tabindex="-1" data-uk-form-password="{ lblShow: '<?= __('Show') ?>', lblHide: '<?= __('Hide') ?>' }"><?= __('Show') ?></a>
        </div>
    </div>

    <p class="uk-form-row">
        <button class="uk-button uk-button-primary uk-button-large uk-width-1-1" type="submit"><?= __('Confirm') ?></button>
    </p>

    <?php $view->token()->get() ?>

</form>
