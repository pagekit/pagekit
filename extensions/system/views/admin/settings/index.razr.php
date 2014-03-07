@style('system', 'system/css/system.css')
@script('settings', 'system/js/settings/index.js', 'requirejs')

<h2 class="uk-h3">@trans('System')</h2>

<ul class="uk-grid pk-system" data-uk-grid-margin>

    @if (user.hasAccess('system: access settings'))
    <li class="uk-width">
        <a class="uk-panel pk-system-hover" href="@url('@system/settings/index')">
            <img src="@url('asset://system/images/icon-settings-system.svg')" width="50" height="50" alt="@trans('Settings')">
            <p>@trans('Settings')</p>
        </a>
    </li>
    @endif

    @if (user.hasAccess('system: manage extensions'))
    <li class="uk-width">
        <a class="uk-panel pk-system-hover" href="@url('@system/extensions/index')">
            <img src="@url('asset://system/images/icon-settings-extensions.svg')" width="50" height="50" alt="@trans('Extensions')">
            <p>@trans('Extensions')</p>
        </a>
    </li>
    @endif

    @if (user.hasAccess('system: manage themes'))
    <li class="uk-width">
        <a class="uk-panel pk-system-hover" href="@url('@system/themes/index')">
            <img src="@url('asset://system/images/icon-settings-themes.svg')" width="50" height="50" alt="@trans('Themes')">
            <p>@trans('Themes')</p>
        </a>
    </li>
    @endif

    <li class="uk-width">
        <a class="uk-panel pk-system-hover" href="@url('@system/dashboard/settings')">
            <img src="@url('asset://system/images/icon-settings-dashboard.svg')" width="50" height="50" alt="@trans('Dashboard')">
            <p>@trans('Dashboard')</p>
        </a>
    </li>

    @if (user.hasAccess('system: manage media'))
    <li class="uk-width">
        <a class="uk-panel pk-system-hover" href="@url('@system/system/storage')">
            <img src="@url('asset://system/images/icon-settings-storage.svg')" width="50" height="50" alt="@trans('Storage')">
            <p>@trans('Storage')</p>
        </a>
    </li>
    @endif

    @if (user.hasAccess('system: software updates'))
    <li class="uk-width">
        <a class="uk-panel pk-system-hover" href="@url('@system/update/index')">
            <img src="@url('asset://system/images/icon-settings-update.svg')" width="50" height="50" alt="@trans('Update')">
            <p>@trans('Update')</p>
        </a>
    </li>
    @endif

    @if (user.hasAccess('system: manage url aliases'))
    <li class="uk-width">
        <a class="uk-panel pk-system-hover" href="@url('@system/alias/index')">
            <img src="@url('asset://system/images/icon-settings-urlaliases.svg')" width="50" height="50" alt="@trans('URL Aliases')">
            <p>@trans('URL Aliases')</p>
        </a>
    </li>
    @endif

    <li class="uk-width">
        <a class="uk-panel pk-system-hover" href="@url('@system/system/info')">
            <img src="@url('asset://system/images/icon-settings-info.svg')" width="50" height="50" alt="@trans('Info')">
            <p>@trans('Info')</p>
        </a>
    </li>

    <li class="uk-width">
        <a id="clearCache" class="uk-panel pk-system-hover" href="@url('@system/settings/clearcache')">
            <img src="@url('asset://system/images/icon-settings-cache.svg')" width="50" height="50" alt="@trans('Clear Cache')">
            <p>@trans('Clear Cache')</p>
        </a>
    </li>

    <li class="uk-width">
        <a class="uk-panel pk-system-hover" href="@url('@system/marketplace/index')">
            <img src="@url('asset://system/images/icon-settings-marketplace.svg')" width="50" height="50" alt="@trans('Marketplace')">
            <p>@trans('Marketplace')</p>
        </a>
    </li>

</ul>

<hr class="uk-margin-large">

<h2 class="uk-h3">@trans('Extensions')</h2>

<ul class="uk-grid pk-system" data-uk-grid-margin>

    @foreach (app.extensions as extension)
    @if (extension.config.settings)
    @set (package = app.extensions.repository.findPackage(extension.name))
    <li class="uk-width">
        <a class="uk-panel pk-system-hover" href="@url('@system/extensions/settings', ['name' => extension.name])">
            <img class="uk-img-preserve" src="@(package.extra.image ? url(package.repository.path ~ '/' ~ package.name ~ '/' ~ package.extra.image) :  url('asset://system/images/placeholder-icon.svg'))" width="50" height="50" alt="@package.title">
            <p>@package.title</p>
        </a>
    </li>
    @endif
    @endforeach

</ul>

<div id="modal-clearcache" class="uk-modal">
    <div class="uk-modal-dialog">

        <h4>@trans('Select caches to clear:')</h4>

        <form class="uk-form" action="@url('@system/system/clearcache')" method="post">

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