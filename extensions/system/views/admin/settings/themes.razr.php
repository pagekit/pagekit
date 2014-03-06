@style('system', 'system/css/system.css')
@script('updates', 'system/js/settings/updates.js', ['requirejs'])

<div id="js-themes" data-api="@api" data-key="@key" data-url="@url.to('@system/package/install')" data-packages="@packagesJson|e">

    <ul class="uk-tab" data-uk-tab="{ connect:'#tab-content' }">
        <li class="uk-active"><a href="#">@trans('All')</a></li>
        <li><a href="#">@trans('Updates') <i class="uk-icon-spinner uk-icon-spin js-updates"></i></a></li>
        <li><a href="#">@trans('Install')</a></li>
    </ul>

    <ul id="tab-content" class="uk-switcher uk-margin">
        <li>

            <div class="uk-grid uk-grid-width-medium-1-2 uk-grid-width-xlarge-1-3" data-uk-grid-margin data-uk-grid-match="{target:'.uk-panel'}">
                @foreach (packages as name => package)
                <div>
                    <div class="uk-panel uk-panel-box pk-themes-panel">
                        <div class="uk-panel-teaser uk-overlay-toggle">
                            <img src="@(package.extra.image ? url.to(package.repository.path ~ '/' ~ package.name ~ '/' ~ package.extra.image) : url.to('asset://system/images/placeholder-800x600.svg'))" width="800" height="600" alt="@package.title">
                            <div class="uk-overlay-area">
                                <div class="uk-overlay-area-content">
                                    @package.description
                                    <ul class="uk-subnav uk-subnav-line uk-margin-remove pk-subnav-muted ">
                                        <li><a href="@(package.author.homepage ?: '#')">@trans('by %author%', ['%author%' => package.author.name])</a></li>
                                        @if (package.author.support)
                                        <li><a href="@package.author.support">@trans('Support')</a></li>
                                        @endif
                                    </ul>
                                    <ul class="uk-list">
                                        <li class="uk-text-truncate"><strong>@trans('Version'):</strong> @package.version</li>
                                        <li class="uk-text-truncate"><strong>@trans('Path'):</strong> /@package.name</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <h2 class="uk-float-left uk-panel-title uk-margin-remove">
                            @package.title
                            @if (current == package)
                            <span class="uk-badge">@trans('Active')</span>
                            @endif
                        </h2>
                        @if (current == package)
                        <div class="uk-float-right">
                            <a class="uk-button" href="@url.to('@system/themes/settings', ['name' => name])" title="@trans('Click for settings')">@trans('Settings')</a>
                        </div>
                        @else
                        <div class="uk-float-right">
                            <a class="uk-button uk-button-primary" href="@url.to('@system/themes/enable', ['name' => name])" title="@trans('Click to enable')">@trans('Enable')</a>
                            <a class="uk-button uk-button-danger " href="@url.to('@system/themes/uninstall', ['name' => name])" title="@trans('Click to uninstall')">@trans('Delete')</a>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

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

            <h2>@trans('Install a theme')</h2>

            <form class="uk-form" data-uk-form-file action="@url.to('@system/themes/upload')" target="js-upload-frame" method="post" enctype="multipart/form-data">
                <input type="text" disabled>
                <div class="uk-form-file">
                    <button class="uk-button">@trans('Select')</button>
                    <input type="file" name="file">
                </div>
                <button class="uk-button uk-button-primary" type="submit">@trans('Upload')</button>
            </form>

        </li>
    </ul>

</div>

<iframe id="js-upload-frame" class="pk-iframe-hidden" name="js-upload-frame" src="javascript:" frameborder="0"></iframe>
