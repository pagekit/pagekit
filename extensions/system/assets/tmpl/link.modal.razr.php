<div class="uk-modal">
    <div class="uk-modal-dialog uk-form">
        <h1 class="uk-h3">@trans('Add Link')</h1>
        <div class="uk-form-row">
            <input type="text" class="js-title uk-width-1-1" placeholder="@trans('Title')">
        </div>
        <div class="uk-form-row">
            <input type="text" class="js-link-url uk-width-1-1" placeholder="@trans('URL')">
        </div>
        <div class="js-linkpicker"></div>
        <div class="uk-form-row">
            <button class="js-update uk-button uk-button-primary" type="button">@trans('Update')</button>
            <button class="uk-button uk-modal-close" type="button">@trans('Cancel')</button>
        </div>
    </div>
</div>