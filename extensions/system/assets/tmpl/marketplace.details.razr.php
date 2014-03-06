<div class="uk-modal-dialog">

    <div>
        <h2>{{ title }}</h2>
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

    <iframe src="{{ iframe }}" width="550" height="400" ></iframe>

</div>