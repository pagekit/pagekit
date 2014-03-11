<div class="uk-modal">
    <form class="uk-modal-dialog uk-form">
        <h1 class="uk-h3">@trans('Add Link')</h1>
        <div class="uk-form-row">
            <select class="js-link-types uk-width-1-1" name="type"></select>
        </div>
        <div class="js-link-edit uk-form-row uk-hidden"></div>
        <div class="uk-form-row">
            <button class="js-update uk-button uk-button-primary" type="submit">@trans('Update')</button>
            <button class="uk-button uk-modal-close" type="button">@trans('Cancel')</button>
        </div>
        <input type="hidden" class="js-link-url">
    </form>
</div>