<?php $app['scripts']->queue('uikit-form-password') ?>

<form class="uk-article uk-form uk-form-stacked" action="<?= $app['url']->get('@system/profile/save') ?>" method="post">

    <h1 class="uk-article-title"><?= __('Your Profile') ?></h1>

    <div class="uk-form-row">
        <label for="form-name" class="uk-form-label"><?= __('Name') ?></label>
        <div class="uk-form-controls">
            <input id="form-name" class="uk-form-width-large" type="text" name="user[name]" value="<?= $user->getName() ?>" required>
        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-name" class="uk-form-label"><?= __('Email') ?></label>
        <div class="uk-form-controls">
            <input id="form-name" class="uk-form-width-large" type="text" name="user[email]" value="<?= $user->getEmail() ?>" required>
        </div>
    </div>

    <div class="uk-form-row js-password">
        <a href="#" data-uk-toggle="{ target: '.js-password' }"><?= __('Change password') ?></a>
    </div>

    <div class="uk-form-row js-password uk-hidden">
        <label for="form-password-old" class="uk-form-label"><?= __('Current Password') ?></label>
        <div class="uk-form-controls">
            <div class="uk-form-password">
                <input id="form-password-old" class="uk-form-width-large" type="password" name="user[password_old]" value="">
                <a href="" class="uk-form-password-toggle" data-uk-form-password="{ lblShow: '<?= __('Show') ?>', lblHide: '<?= __('Hide') ?>' }"><?= __('Show') ?></a>
            </div>
        </div>
    </div>

    <div class="uk-form-row js-password uk-hidden">
        <label for="form-password-new" class="uk-form-label"><?= __('New Password') ?></label>
        <div class="uk-form-controls">
            <div class="uk-form-password">
                <input id="form-password-new" class="uk-form-width-large" type="password" name="user[password_new]" value="">
                <a href="" class="uk-form-password-toggle" data-uk-form-password="{ lblShow: '<?= __('Show') ?>', lblHide: '<?= __('Hide') ?>' }"><?= __('Show') ?></a>
            </div>
        </div>
    </div>

    <div class="uk-form-row">
        <button class="uk-button uk-button-primary" type="submit"><?= __('Save') ?></button>
    </div>

    <?php $this['token']->generate() ?>

</form>