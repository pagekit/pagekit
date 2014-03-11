<div class="uk-modal-dialog uk-modal-dialog-large pk-marketplace-modal-dialog">

    <div class="pk-marketplace-modal-action">
        {{ #installed }}
        <button class="uk-button" disabled>@trans('Installed')</button>
        {{ /installed }}

        {{ ^installed }}
        <button class="uk-button uk-button-primary" data-install="{{ name }}">
            {{ #install }}@trans('Install'){{ /install }}
            {{ #update }}@trans('Update'){{ /update }}
        </button>
        {{ /installed }}
    </div>

    <iframe src="{{ iframe }}" class="uk-width-1-1 uk-height-1-1"></iframe>

</div>