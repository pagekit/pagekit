<div class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-large uk-form">

        <div data-screen="settings">
            <h1 class="uk-h3"><?= __('Add Video') ?></h1>

            <div class="uk-grid">
                <div class="uk-width-1-3 uk-text-center">
                    <div class="js-video-preview">&nbsp;</div>
                </div>

                <div class="uk-width-2-3">

                    <div class="uk-form-row">
                        <input type="text" class="js-url uk-width-4-5" placeholder="<?= __('URL') ?>">
                        <button type="button" class="uk-button uk-float-right uk-width-1-6" data-goto="finder"><?= __('Select video') ?></button>
                    </div>

                </div>
            </div>

            <div class="uk-form-row uk-margin-top">
                <button class="js-update uk-button uk-button-primary" type="button"><?= __('Update') ?></button>
                <button class="uk-button uk-modal-close" type="button"><?= __('Cancel') ?></button>
            </div>
        </div>
        <div class="uk-hidden" data-screen="finder">
            <h1 class="uk-h3"><?= __('Select Video') ?></h1>
            <div class="js-finder"></div>
            <div class="uk-margin-top">
                <button class="uk-button uk-button-primary js-select-image" disabled type="button"><?= __('Select') ?></button>
                <button class="uk-button" type="button" data-goto="settings"><?= __('Cancel') ?></button>
            </div>
        </div>
    </div>
</div>