<form class="uk-form uk-form-horizontal" action="@url('@system/extensions/savesettings', ['name' => 'hello'])" method="post">

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <h2 class="uk-margin-remove"><?= __('Edit Settings') ?></h2>

        </div>
        <div data-uk-margin>

            <button class="uk-button uk-button-primary" type="submit"><?= __('Save') ?></button>
            <a class="uk-button" href="@url('@system/system')"><?= __('Close') ?></a>

        </div>
    </div>

    <div class="uk-form-row">
        <label for="form-hello-message" class="uk-form-label"><?= __('Message') ?></label>
        <div class="uk-form-controls">
            <input id="form-hello-message" type="text" name="params[message]" value="@($params['message'])">
        </div>
    </div>

    <p><?= __('This settings page is just for demonstration purposes.') ?></p>

</form>