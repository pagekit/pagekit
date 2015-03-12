<div class="js-editor-image uk-overlay uk-display-block">
    {{#if src}}
        <img src="{{{src}}}" alt="{{{alt}}}">
    {{else}}
        <div class="uk-placeholder uk-placeholder-large uk-text-center uk-vertical-align">
            <div class="uk-vertical-align-middle"><img data-js-no-parse src="<?= $view->url()->getStatic('extensions/system/assets/images/placeholder-editor-image.svg') ?>" width="60" height="60" alt="<?= __('Placeholder Image') ?>"></div>
        </div>
    {{/if}}

    <div class="uk-overlay-area pk-overlay-border">
        <div class="uk-overlay-area-content">
            <h3 class="uk-margin-small-bottom"><?= __('Image') ?></h3>
            <div data-uk-margin>
                <button class="uk-button uk-button-primary js-config" type="button"><?= __('Settings') ?></button>
                <button class="uk-button pk-button-danger js-remove" type="button"><?= __('Delete') ?></button>
            </div>
        </div>
    </div>
</div>
