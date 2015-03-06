<?php $app['scripts']->add('uikit-form-password') ?>

<form class="uk-article uk-form" action="<?= $view->url('@system/resetpassword/confirm', ['user' => $username, 'key' => $activation]) ?>" method="post">

    <h1 class="uk-article-title"><?= __('Password confirmation') ?></h1>

    <div class="uk-form-row js-password">
        <label for="form-password" class="uk-form-label"><?= __('Enter your new password below.') ?></label>
        <div class="uk-form-controls">
            <div class="uk-form-password">
                <input id="form-password" class="uk-form-width-large" type="password" name="password" value="">
                <a href="" class="uk-form-password-toggle" data-uk-form-password="{ lblShow: '<?= __('Show') ?>', lblHide: '<?= __('Hide') ?>' }"><?= __('Show') ?></a>
            </div>
        </div>
    </div>

    <div class="uk-form-row">
        <button class="uk-button uk-button-primary" type="submit"><?= __('Submit') ?></button>
    </div>

    <?php $view->token()->get() ?>

</form>