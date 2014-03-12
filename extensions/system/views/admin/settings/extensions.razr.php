@style('system', 'system/css/system.css')
@script('updates', 'system/js/settings/updates.js', 'requirejs')
@script('marketplace', 'system/js/settings/marketplace.js', 'requirejs')

<div id="js-extensions" data-api="@api" data-key="@key" data-url="@url('@system/package/install')" data-installed="@packagesJson|e">

    <ul class="uk-tab" data-uk-tab="{ connect: '#tab-content' }">
        <li class="uk-active"><a href="#">@trans('All')</a></li>
        <li><a href="#">@trans('Updates') <i class="uk-icon-spinner uk-icon-spin js-updates"></i></a></li>
        <li><a href="#">@trans('Install')</a></li>
        <li><a href="#">@trans('Marketplace')</a></li>
    </ul>

    <ul id="tab-content" class="uk-switcher uk-margin">
        <li>

            <table class="uk-table uk-table-hover">
                <thead>
                    <tr>
                        <th colspan="2">@trans('Name')</th>
                        <th colspan="2">@trans('Description')</th>
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
                        <td>
                            <h2 class="pk-extensions-heading">@package.title</h2>
                            @if (extension)
                            <ul class="uk-subnav uk-subnav-line uk-margin-remove uk-text-nowrap">
                                @if (extension.config.settings)
                                <li><a href="@url('@system/extensions/settings', ['name' => extension.name])">@trans('Settings')</a></li>
                                @endif
                                <li><a href="@url('@system/permission/index')#ext-@extension.name">@trans('Permissions')</a></li>
                            </ul>
                            @endif
                        </td>
                        <td>
                            <div class="pk-extensions-margin">@package.description</div>
                            <ul class="uk-subnav uk-subnav-line uk-margin-remove pk-subnav-muted ">
                                <li><a href="@(package.author.homepage ?: '#')">@trans('by %author%', ['%author%' => package.author.name])</a></li>
                                @if (package.author.support)
                                <li><a href="@package.author.support">@trans('Support')</a></li>
                                @endif
                            </ul>
                        </td>
                        <td class="pk-table-max-width-200">
                            <ul class="uk-list pk-extensions-list pk-extensions-margin">
                                <li class="uk-text-truncate"><strong>@trans('Version'):</strong> @package.version</li>
                                <li class="uk-text-truncate"><strong>@trans('Path'):</strong> /@package.name</li>
                            </ul>
                        </td>
                        <td class="uk-text-center">
                            @if (extension)
                            <a class="uk-button uk-button-success pk-extensions-margin" href="@url('@system/extensions/disable', ['name' => name])" title="@trans('Click to disable')">@trans('Enabled')</a>
                            @else
                            <a class="uk-button pk-extensions-margin" href="@url('@system/extensions/enable', ['name' => name])" title="@trans('Click to enable')">@trans('Disabled')</a>
                            @endif
                        </td>
                        <td>
                            <a class="uk-button uk-button-danger pk-extensions-margin uk-invisible" href="@url('@system/extensions/uninstall', ['name' => name])" title="@trans('Delete')">@trans('Delete')</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </li>
        <li class="js-update-table">

            <div class="uk-alert uk-alert-warning uk-hidden" data-msg="no-connection">
                @trans('An error occurred in retrieving update information. Please try again later.')
            </div>

            <div class="uk-alert uk-alert-info uk-hidden" data-msg="no-updates">
                @trans('No updates found.')
            </div>

        </li>
        <li>

            <h2>@trans('Install an extension')</h2>

            <form class="uk-form" data-uk-form-file action="@url('@system/extensions/upload')" target="js-upload-frame" method="post" enctype="multipart/form-data">
                <input type="text" disabled>
                <div class="uk-form-file">
                    <button class="uk-button">@trans('Select')</button>
                    <input type="file" name="file">
                </div>
                <button class="uk-button uk-button-primary" type="submit">@trans('Upload')</button>
            </form>

        </li>
        <li>

            <form class="js-marketplace-form uk-form pk-options uk-clearfix">
                <div class="uk-float-left">
                    <input type="text" name="q" placeholder="@trans('Search')">
                    <input type="hidden" name="type" value="extension">
                </div>
            </form>

            <p class="uk-alert uk-alert-info uk-hidden" data-msg="no-packages">@trans('No extensions found.')</p>
            <p class="uk-alert uk-alert-warning uk-hidden" data-msg="no-connection">@trans('Cannot connect to the Marketplace. Please try again later.')</p>

            <div class="js-marketplace-content"></div>
            <div class="js-marketplace-details uk-modal"></div>

        </li>
    </ul>

</div>

<iframe id="js-upload-frame" class="pk-iframe-hidden" name="js-upload-frame" src="javascript:" frameborder="0"></iframe>
