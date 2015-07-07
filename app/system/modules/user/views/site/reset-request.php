<form class="uk-article uk-form uk-form-stacked" action="<?= $view->url('@user/resetpassword/request') ?>" method="post">

    <h1 class="uk-article-title"><?= __('Forgot password') ?></h1>

    <p><?= __('Please enter your email address. You will receive a link to create a new password via email.') ?></p>

    <div class="uk-form-row">
        <label for="form-username" class="uk-form-label"><?= __('Email') ?></label>
        <div class="uk-form-controls">
            <input id="form-username" type="text" name="email" value="" required autofocus>
        </div>
    </div>

    <div class="uk-form-row">
        <button class="uk-button uk-button-primary" type="submit"><?= __('Submit') ?></button>
    </div>

    <?php $view->token()->get() ?>

</form>
