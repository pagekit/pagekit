@style('system', 'system/css/system.css')
@script('settings', 'system/js/settings/index.js', 'requirejs')

<h2 class="uk-h3">@trans('System')</h2>

<ul class="uk-grid pk-system" data-uk-grid-margin>

    @if (user.hasAccess('system: access settings'))
    <li class="uk-width">
        <a class="uk-panel pk-panel-icon" href="@url.route('@system/settings')">
            <img src="@url.to('asset://system/images/icon-settings.svg')" width="50" height="50" alt="@trans('Settings')">
            <p>@trans('Settings')</p>
        </a>
    </li>
    @endif

    @if (user.hasAccess('system: manage extensions'))
    <li class="uk-width">
        <a class="uk-panel pk-panel-icon" href="@url.route('@system/extensions')">
            <img src="@url.to('asset://system/images/icon-extensions.svg')" width="50" height="50" alt="@trans('Extensions')">
            <p>@trans('Extensions')</p>
        </a>
    </li>
    @endif

    @if (user.hasAccess('system: manage themes'))
    <li class="uk-width">
        <a class="uk-panel pk-panel-icon" href="@url.route('@system/themes')">
            <img src="@url.to('asset://system/images/icon-themes.svg')" width="50" height="50" alt="@trans('Themes')">
            <p>@trans('Themes')</p>
        </a>
    </li>
    @endif

    <li class="uk-width">
        <a class="uk-panel pk-panel-icon" href="@url.route('@system/dashboard/settings')">
            <img src="@url.to('asset://system/images/icon-dashboard.svg')" width="50" height="50" alt="@trans('Dashboard')">
            <p>@trans('Dashboard')</p>
        </a>
    </li>

    @if (user.hasAccess('system: manage media'))
    <li class="uk-width">
        <a class="uk-panel pk-panel-icon" href="@url.route('@system/system/storage')">
            <img src="@url.to('asset://system/images/icon-storage.svg')" width="50" height="50" alt="@trans('Storage')">
            <p>@trans('Storage')</p>
        </a>
    </li>
    @endif

    @if (user.hasAccess('system: software updates'))
    <li class="uk-width">
        <a class="uk-panel pk-panel-icon" href="@url.route('@system/update')">
            <img src="@url.to('asset://system/images/icon-update.svg')" width="50" height="50" alt="@trans('Update')">
            <p>@trans('Update')</p>
        </a>
    </li>
    @endif

    @if (user.hasAccess('system: manage url aliases'))
    <li class="uk-width">
        <a class="uk-panel pk-panel-icon" href="@url.route('@system/alias')">
            <img src="@url.to('asset://system/images/icon-urlaliases.svg')" width="50" height="50" alt="@trans('URL Aliases')">
            <p>@trans('URL Aliases')</p>
        </a>
    </li>
    @endif

    <li class="uk-width">
        <a class="uk-panel pk-panel-icon" href="@url.route('@system/system/info')">
            <img src="@url.to('asset://system/images/icon-info.svg')" width="50" height="50" alt="@trans('Info')">
            <p>@trans('Info')</p>
        </a>
    </li>

    <li class="uk-width">
        <a id="clearCache" class="uk-panel pk-panel-icon" href="@url.route('@system/settings/clearcache')">
            <img src="@url.to('asset://system/images/icon-cache.svg')" width="50" height="50" alt="@trans('Clear Cache')">
            <p>@trans('Clear Cache')</p>
        </a>
    </li>

</ul>

@if (packages && app.user.hasAccess('system: manage extensions'))
<hr class="uk-margin-large">

<h2 class="uk-h3">@trans('Extensions')</h2>

<ul class="uk-grid pk-system" data-uk-grid-margin>

    @foreach (packages as extension => package)
    <li class="uk-width">
        <a class="uk-panel pk-panel-icon" href="@url.route('@system/extensions/settings', ['name' => extension])">
            <img class="uk-img-preserve" src="@(package.extra.image ? url.to(package.repository.path ~ '/' ~ package.name ~ '/' ~ package.extra.image) : url.to('asset://system/images/placeholder-icon.svg'))" width="50" height="50" alt="@package.title">
            <p>@package.title</p>
        </a>
    </li>
    @endforeach

</ul>
@endif

<div id="modal-clearcache" class="uk-modal">
    <div class="uk-modal-dialog">

        <h4>@trans('Select caches to clear:')</h4>

        <form class="uk-form" action="@url.route('@system/system/clearcache')" method="post">

            <div class="uk-form-row">
                <div class="uk-form-controls uk-form-controls-text">
                    <p class="uk-form-controls-condensed">
                        <label><input type="checkbox" name="caches[cache]" value="1" checked> @trans('System Cache')</label>
                    </p>
                </div>
            </div>
            <div class="uk-form-row">
                <div class="uk-form-controls uk-form-controls-text">
                    <p class="uk-form-controls-condensed">
                        <label><input type="checkbox" name="caches[templates]" value="1" checked> @trans('Rendered Templates')</label>
                    </p>
                </div>
            </div>
            <div class="uk-form-row">
                <div class="uk-form-controls uk-form-controls-text">
                    <p class="uk-form-controls-condensed">
                        <label><input type="checkbox" name="caches[temp]" value="1"> @trans('Temporary Files')</label>
                    </p>
                </div>
            </div>
            <p>
                <button class="uk-button uk-button-primary" type="submit">@trans('Clear')</button>
                <button class="uk-button uk-modal-close" type="submit">@trans('Cancel')</button>
            </p>
            @token()

        </form>

    </div>
</div>