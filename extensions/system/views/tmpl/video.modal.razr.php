<div class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-large uk-form">

        <div data-screen="settings">
            <h1 class="uk-h3">@trans('Add Video')</h1>

            <div class="uk-grid">
                <div class="uk-width-1-3 uk-text-center">
                    <div class="js-video-preview">&nbsp;</div>
                </div>

                <div class="uk-width-2-3">

                    <div class="uk-form-row">
                        <input type="text" class="js-url uk-width-4-5" placeholder="@trans('URL')">
                        <button type="button" class="uk-button uk-float-right uk-width-1-6" data-goto="finder">@trans('Select video')</button>
                    </div>

                </div>
            </div>

            <div class="uk-form-row uk-margin-top">
                <button class="js-update uk-button uk-button-primary" type="button">@trans('Update')</button>
                <button class="uk-button uk-modal-close" type="button">@trans('Cancel')</button>
            </div>
        </div>
        <div class="uk-hidden" data-screen="finder">
            <h1 class="uk-h3">@trans('Select Video')</h1>
            <div class="js-finder"></div>
            <div class="uk-margin-top">
                <button class="uk-button uk-button-primary js-select-image" disabled type="button">@trans('Select')</button>
                <button class="uk-button" type="button" data-goto="settings">@trans('Cancel')</button>
            </div>
        </div>
    </div>
</div>