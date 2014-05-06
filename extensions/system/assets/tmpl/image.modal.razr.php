<div class="uk-modal">
    <div class="uk-modal-dialog uk-form">
        <div data-screen="settings">
            <h1 class="uk-h3">@trans('Add Image')</h1>

                <div class="uk-form-row uk-text-center">
                    <img class="js-img-preview" alt="Preview image">
                </div>

                <div class="uk-form-row">
                    <a href="#" data-goto="finder">@trans('Select image')</a>
                </div>

                <div class="uk-form-row">
                    <input type="text" class="js-url uk-width-1-1" placeholder="@trans('URL')">
                </div>
                <div class="uk-form-row">
                    <input type="text" class="js-title uk-width-1-1" placeholder="@trans('Alt')">
                </div>

            <div class="uk-form-row uk-margin-top">
                <button class="js-update uk-button uk-button-primary" type="button">@trans('Update')</button>
                <button class="uk-button uk-modal-close" type="button">@trans('Cancel')</button>
            </div>
        </div>
        <div class="uk-hidden" data-screen="finder">
            <h1 class="uk-h3">@trans('Select Image')</h1>
            <div class="js-finder"></div>
            <div class="uk-margin-top">
                <button class="uk-button uk-button-primary js-select-image" disabled type="button">@trans('Select')</button>
                <button class="uk-button" type="button" data-goto="settings">@trans('Cancel')</button>
            </div>
        </div>
    </div>
</div>