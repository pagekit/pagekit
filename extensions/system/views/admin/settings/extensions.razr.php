@style('system', 'system/css/system.css')
@script('updates', 'system/js/settings/updates.js', 'marketplace')
@script('marketplace', 'system/js/settings/marketplace.js', 'requirejs')

<div id="js-extensions" class="uk-grid" data-api="@api" data-key="@key" data-url="@url('@system/package/install')" data-installed="@packagesJson|e" data-uk-grid-margin data-uk-grid-match>

    <div class="pk-sidebar uk-width-medium-1-4">

        <ul class="uk-nav uk-nav-side" data-uk-switcher="{connect:'#tab-content', toggle:' > *:not(.uk-nav-header)'}">
            <li class="uk-active"><a href="#">@trans('Installed')</a></li>
            <li><a href="#">@trans('Updates') <i class="uk-icon-spinner uk-icon-spin js-updates"></i></a></li>
            <li><a href="#">@trans('Install')</a></li>
            <li class="uk-nav-header">@trans('Marketplace')</li>
            <li><a href="#">@trans('All')</a></li>
        </ul>

    </div>
    <div class="pk-content uk-width-medium-3-4">

        <ul id="tab-content" class="uk-switcher uk-margin">
            <li>

                <table class="uk-table uk-table-hover">
                    <thead>
                        <tr>
                            <th colspan="2">@trans('Name')</th>
                            <th>@trans('Version')</th>
                            <th class="pk-table-width-minimum uk-text-center">@trans('Status')</th>
                            <th class="pk-table-width-minimum"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (packages as name => package)
                        @set (extension = app['extensions'].get(name))
                        <tr class="@if (!extension)uk-visible-hover-inline@endif @if (!extension)pk-table-disable@endif">
                            <td class="pk-table-width-minimum">
                                <img class="uk-img-preserve" src="@( package.extra.image ? url(package.repository.path ~ '/' ~ package.name ~ '/' ~ package.extra.image) :  url('asset://system/images/placeholder-icon.svg'))" width="50" height="50" alt="@package.title">
                            </td>
                            <td class="uk-table-middle">
                                <h2 class="uk-h3 uk-margin-remove">@package.title</h2>
                                <ul class="uk-subnav uk-subnav-line uk-margin-remove uk-text-nowrap">
                                    <li><a href="">@trans('Details')</a></li>
                                    @if (extension)
                                    @if (extension.config.settings)
                                    <li><a href="@url('@system/extensions/settings', ['name' => extension.name])">@trans('Settings')</a></li>
                                    @endif
                                    <li><a href="@url('@system/permission/index')#ext-@extension.name">@trans('Permissions')</a></li>
                                    @endif
                                </ul>
                            </td>
                            <td class="uk-table-middle pk-table-max-width-200">
                                <ul class="uk-list uk-margin-remove">
                                    <li class="uk-text-truncate">@package.version</li>
                                    <li class="uk-text-truncate">/@package.name</li>
                                </ul>
                            </td>
                            <td class="uk-table-middle uk-text-center">
                                @if (extension)
                                <a class="uk-button uk-button-success" href="@url('@system/extensions/disable', ['name' => name])" title="@trans('Click to disable')">@trans('Enabled')</a>
                                @else
                                <a class="uk-button" href="@url('@system/extensions/enable', ['name' => name])" title="@trans('Click to enable')">@trans('Disabled')</a>
                                @endif
                            </td>
                            <td class="uk-table-middle">
                                <a class="uk-button uk-button-danger uk-invisible" href="@url('@system/extensions/uninstall', ['name' => name])" title="@trans('Delete')">@trans('Delete')</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </li>
            <li class="js-update">

                <div class="uk-alert uk-alert-warning uk-hidden" data-msg="no-connection">
                    @trans('An error occurred in retrieving update information. Please try again later.')
                </div>

                <div class="uk-alert uk-alert-info uk-hidden" data-msg="no-updates">
                    @trans('No extension updates found.')
                </div>

            </li>
            <li class="js-upload">

                <h2 class="pk-form-heading">@trans('Install an extension')</h2>

                <form class="uk-form" action="@url('@system/package/upload', ['type' => 'extension'])" data-uk-form-file>
                    <input type="text" disabled>
                    <div class="uk-form-file">
                        <button class="uk-button">@trans('Select')</button>
                        <input type="file" name="file">
                    </div>
                    <button class="js-upload-button uk-button uk-button-primary">@trans('Upload')</button>
                </form>

                <div class="js-upload-modal uk-modal"></div>

            </li>
            <li class="js-marketplace">

                <form class="uk-form pk-options uk-clearfix">
                    <div class="uk-float-left">
                        <input type="text" name="q" placeholder="@trans('Search')">
                        <input type="hidden" name="type" value="extension">
                    </div>
                </form>

                <p class="uk-alert uk-alert-info uk-hidden" data-msg="no-packages">@trans('No extensions found.')</p>
                <p class="uk-alert uk-alert-warning uk-hidden" data-msg="no-connection">@trans('Cannot connect to the marketplace. Please try again later.')</p>

                <div class="js-marketplace-content"></div>
                <div class="js-marketplace-details uk-modal"></div>

            </li>
        </ul>

    </div>

</div>
