<div class="uk-modal">
    <div class="uk-modal-dialog uk-form uk-form-stacked">
        <div data-screen="settings">
            <h1 class="uk-h3"><?= __('Add Image') ?></h1>
            <div class="uk-form-row">
                <div class="uk-form-controls">
                    <div class="js-img-preview pk-thumbnail pk-thumbnail-image"></div>
                    <p class="uk-margin-small-top"><a href="#" data-goto="finder"><?= __('Select image') ?></a></p>
                </div>
            </div>
            <div class="uk-form-row">
                <label for="form2-url" class="uk-form-label"><?= __('URL') ?></label>
                <div class="uk-form-controls">
                    <input id="form2-url" type="text" class="js-url uk-width-1-1">
                </div>
            </div>
            <div class="uk-form-row">
                <label for="form2-alt" class="uk-form-label"><?= __('Alt') ?></label>
                <div class="uk-form-controls">
                    <input id="form2-alt" type="text" class="js-title uk-width-1-1">
                </div>
            </div>
            <div class="uk-form-row uk-margin-top">
                <button class="js-update uk-button uk-button-primary" type="button"><?= __('Update') ?></button>
                <button class="uk-button uk-modal-close" type="button"><?= __('Cancel') ?></button>
            </div>
        </div>
        <div class="uk-hidden" data-screen="finder">
            <h1 class="uk-h3"><?= __('Select Image') ?></h1>
            <div class="js-finder"></div>
            <div class="uk-margin-top">
                <button class="uk-button uk-button-primary js-select-image" disabled type="button"><?= __('Select') ?></button>
                <button class="uk-button" type="button" data-goto="settings"><?= __('Cancel') ?></button>
            </div>
        </div>
    </div>
</div>