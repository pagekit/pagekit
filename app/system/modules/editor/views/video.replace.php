<div class="js-editor-video uk-overlay uk-overlay-hover uk-display-block">
    ${preview}
    <div class="uk-placeholder uk-placeholder-large uk-text-center uk-vertical-align ${src ? 'uk-hidden' : ''}">
        <div class="uk-vertical-align-middle"><img data-js-no-parse src="<?= $view->url()->getStatic('app/system/modules/editor/assets/images/placeholder-video.svg') ?>" width="60" height="60" alt="<?= __('Placeholder Video') ?>"></div>
    </div>
    <div class="uk-overlay-panel uk-overlay-background uk-overlay-fade uk-flex uk-flex-center uk-flex-middle uk-text-center pk-overlay-border">
        <div>
            <h3 class="uk-margin-small-bottom"><?= __('Video') ?></h3>
            <div data-uk-margin>
                <button class="uk-button uk-button-primary js-config" type="button"><?= __('Settings') ?></button>
                <button class="uk-button uk-button-danger js-remove" type="button"><?= __('Delete') ?></button>
            </div>
        </div>
    </div>
</div>
