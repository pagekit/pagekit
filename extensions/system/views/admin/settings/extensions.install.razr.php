@if (checksum === false)
<p class="uk-alert uk-alert-error">@trans('The checksum of the uploaded extension does not match the one from the repository. The uploaded extension might be manipulated.')</p>
@endif

@if (status == 'update')
<p class="uk-alert uk-alert-success">@trans('There is an update available for the uploaded extension. Please consider installing it instead.')</p>
@endif

@if (status == 'old')
<p class="uk-alert uk-alert-warning">@trans('You are trying to install an older version of a extension that is already installed.')</p>
@endif

<dl>
    <dt>@trans('Name')</dt>
    <dd>@package.name</dd>
    <dt>@trans('Version')</dt>
    <dd>@package.version</dd>
    <dt>@trans('Type')</dt>
    <dd>@package.type</dd>
    <dt>@trans('Title')</dt>
    <dd>@package.title</dd>
    <dt>@trans('Description')</dt>
    <dd>@package.description</dd>
    <dt>@trans('Keywords')</dt>
    <dd>@package.keywords|implode(', ')</dd>
    <dt>@trans('Homepage')</dt>
    <dd>@package.homepage</dd>
    <dt>@trans('License')</dt>
    <dd>@package.license|implode(', ')</dd>
    <dt>@trans('Authors')</dt>
    <dd>@package.authors</dd>
    <dt>@trans('Release Date')</dt>
    <dd>@package.releaseDate|date('Y-m-d')</dd>
</dl>

<p>
    <a class="uk-button uk-button-primary" href="@url('@system/extensions/install', ['path' => path])">@trans('Install')</a>
    <a class="uk-button" href="@url('@system/extensions/index')">@trans('Cancel')</a>
</p>