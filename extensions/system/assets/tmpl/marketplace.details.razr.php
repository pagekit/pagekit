<div class="uk-modal-dialog uk-modal-dialog-large pk-marketplace-modal-dialog">

    <div class="pk-marketplace-modal-action">
        {{#if installed}}
        <button class="uk-button" disabled>@trans('Installed')</button>
        {{else}}
        <button class="uk-button uk-button-primary" data-install="{{name}}">
            {{#if install}}@trans('Install'){{/if}}
            {{#if update}}@trans('Update'){{/if}}
        </button>
        {{/if}}
    </div>

    <iframe src="{{iframe}}" class="uk-width-1-1 uk-height-1-1"></iframe>

</div>