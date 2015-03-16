<div class="js-editor-image uk-overlay uk-overlay-hover uk-display-block">
    <img src="${src}" alt="${alt}" ${!src ? 'class="uk-hidden"' : ''}>
    <div class="uk-placeholder uk-placeholder-large uk-text-center uk-vertical-align ${src ? 'uk-hidden' : ''}">
        <div class="uk-vertical-align-middle"><img data-js-no-parse src="<?= $view->url()->getStatic('extensions/system/assets/images/placeholder-editor-image.svg') ?>" width="60" height="60" alt="<?= __('Placeholder Image') ?>"></div>
    </div>
    <div class="uk-overlay-panel uk-overlay-background uk-overlay-fade uk-flex uk-flex-center uk-flex-middle uk-text-center pk-overlay-border">
        <div>
            <h3 class="uk-margin-small-bottom"><?= __('Image') ?></h3>
            <div data-uk-margin>
                <button class="uk-button uk-button-primary js-config" type="button"><?= __('Settings') ?></button>
                <button class="uk-button pk-button-danger js-remove" type="button"><?= __('Delete') ?></button>
            </div>
        </div>
    </div>
</div>
