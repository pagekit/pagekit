<div class="uk-modal">
    <div class="uk-modal-dialog uk-form">
        <h1 class="uk-h3">@trans('Add Link')</h1>
        <div class="uk-form-row">
            <label class="uk-form-label" for="form-title">@trans('Title')</label>
            <div class="uk-form-controls">
                <input id="form-title" type="text" class="js-title uk-width-1-1">
            </div>
        </div>
        <div class="js-linkpicker uk-form-row"></div>
        <div class="uk-form-row">
            <button class="js-update uk-button uk-button-primary" type="button">@trans('Update')</button>
            <button class="uk-button uk-modal-close" type="button">@trans('Cancel')</button>
        </div>
    </div>
</div>