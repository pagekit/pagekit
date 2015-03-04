<?php $app['scripts']->add('settings', 'extensions/system/modules/user/assets/js/login.js', 'requirejs') ?>

<div class="uk-form-row">
    <label class="uk-form-label"><?= __('Login Redirect') ?></label>
    <div class="uk-form-controls uk-form-controls-text">
        <input type="hidden" name="widget[settings][redirect.login]" value="<?= $widget->get('redirect.login') ?>">
    </div>
</div>
<div class="uk-form-row">
    <label class="uk-form-label"><?= __('Logout Redirect') ?></label>
    <div class="uk-form-controls uk-form-controls-text">
        <input type="hidden" name="widget[settings][redirect.logout]" value="<?= $widget->get('redirect.logout') ?>">
    </div>
</div>