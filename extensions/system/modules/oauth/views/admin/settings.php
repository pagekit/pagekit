<h2 class="pk-form-heading">{{ 'OAuth' | trans }}</h2>
<div class="uk-button-dropdown" data-uk-dropdown>
    <div class="uk-button">Add Service <i class="uk-icon-caret-down"></i></div>
    <div class="uk-dropdown uk-dropdown-scrollable">
        <ul class="uk-nav uk-nav-dropdown" id="oauth-service-dropdown"></ul>
    </div>
</div>
<p><?= __('Redirect URL: %url%', ['%url%' => $app['oauth']->getRedirectUrl()]) ?></p>
<div id="oauth-service-list" class="uk-form-row">
    <input type="hidden" name="option[system/oauth]" value="">
</div>
<script type="text/tmpl" data-tmpl="oauth.data">
    <?= $app['oauth']->getJsonData() ?>
</script>
