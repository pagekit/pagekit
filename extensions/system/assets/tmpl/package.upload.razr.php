<div class="uk-modal-dialog">

    <div class="uk-alert uk-alert-danger uk-hidden" data-msg="checksum-mismatch">
        @trans('The checksum of the uploaded package does not match the one from the marketplace. The file might be manipulated.')
    </div>

    <div class="uk-alert uk-alert-success uk-hidden" data-msg="update-available">
        @trans('There is an update available for the uploaded package. Please consider installing it instead.')
    </div>

    <div class="uk-grid">
        <div class="uk-width-1-1">
            <img class="uk-align-left uk-margin-bottom-remove" src="{{package.extra.image}}" width="50" height="50" alt="{{package.title}}">
            <h1 class="uk-h2 uk-margin-remove">{{package.title}}</h1>
            <ul class="uk-subnav uk-subnav-line uk-margin-top-remove">
                <li>{{package.author.name}}</li>
                <li>@trans('Version') {{package.version}}</li>
            </ul>
        </div>
    </div>

    <hr class="uk-grid-divider">

    <div class="uk-grid">
        <div class="uk-width-1-2">
            <div>{{package.description}}</div>
            <ul>
                <li>@trans('Path:') {{package.name}}</li>
                <li>@trans('Type:') {{package.type}}</li>
            </ul>
        </div>
    </div>

    <p>
        <button class="uk-button uk-button-primary" data-install="{{install}}">@trans('Install')</button>
        <button class="uk-button uk-modal-close">@trans('Cancel')</button>
    </p>

</div>