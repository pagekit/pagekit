<div class="uk-modal">
    <div class="uk-modal-dialog uk-form uk-form-stacked">
        <h1 class="uk-h3">@trans('Add Link')</h1>
        <div class="uk-form-row">
            <label for="form2-title" class="uk-form-label">@trans('Title')</label>
            <div class="uk-form-controls">
                <input id="form2-title" type="text" class="js-title uk-width-1-1">
            </div>
        </div>
        <div class="js-linkpicker uk-form-row"></div>
        <div class="uk-form-row">
            <button class="js-update uk-button uk-button-primary" type="button">@trans('Update')</button>
            <button class="uk-button uk-modal-close" type="button">@trans('Cancel')</button>
        </div>
    </div>
</div>