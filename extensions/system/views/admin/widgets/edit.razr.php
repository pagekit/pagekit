@script('widget', 'system/js/widgets/widget.js', 'requirejs')

<form class="js-widget uk-form uk-form-horizontal uk-grid uk-grid-divider" data-uk-grid-margin data-uk-grid-match action="@url.route('@system/widgets/save', ['id' => widget.id ?: 0])" method="post">

    <div class="uk-width-medium-3-4">

        <div class="pk-options">
            <button class="uk-button uk-button-primary" type="submit">@trans('Save')</button>
            <a class="uk-button" href="@url.route('@system/widgets/index')">@(widget.id ? trans('Close') : trans('Cancel'))</a>
        </div>

        <ul id="tab-content" class="uk-switcher uk-margin">
            <li>

                <div class="uk-form-row">
                    <label for="form-title" class="uk-form-label">@trans('Title')</label>
                    <div class="uk-form-controls">
                        <p class="uk-form-controls-condensed">
                            <input id="form-title" class="uk-form-width-large" type="text" name="widget[title]" value="@widget.title" required>
                        </p>
                    </div>
                </div>

                <h2 class="pk-form-heading">@trans('Advanced')</h2>

                @set (type = app.widgets.types[widget.type])
                @type.renderForm(widget)

            </li>
            <li>

                <div class="uk-form-row">
                    <label for="form-h-it" class="uk-form-label">@trans('Menu Items')</label>
                    <div class="uk-form-controls">
                        <div class="uk-scrollable-box uk-form-width-large">
                            @foreach (app.menus as id => menu)
                            @set (root = app.menus.getTree(menu.id))
                            @if (id != 'admin' && root.hasChildren())
                            @if (prev)<hr>@endif@set (prev = 1)
                            <h3 class="uk-h4 uk-margin-top-remove">@menu.name</h3>
                            @include('view://system/admin/widgets/select.razr.php', ['root' => root, 'widget' => widget])
                            @endif
                            @endforeach
                            <input type="hidden" name="widget[menuItems][]" value="">
                        </div>
                    </div>
                </div>
                <div class="uk-form-row">
                    <label for="form-position" class="uk-form-label">@trans('Pattern')</label>
                    <div class="uk-form-controls">
                        <textarea class="uk-form-width-large" name="widget[pages]" rows="5">@widget.pages</textarea>
                        <p class="uk-form-help-block">@trans('Enter one page path per line. The "*" character is a wildcard. Exclude pages by prepending an exclamation mark to the path. Example paths: "blog" for the blog page, "blog/*" for blog entries or "!blog" if you want to exclude the blog.')</p>
                    </div>
                </div>

            </li>
            @foreach (additionals as settings)
            <li>
                @settings
            </li>
            @endforeach
        </ul>

        <input type="hidden" name="widget[type]" value="@widget.type">

    </div>
    <div class="uk-width-medium-1-4 pk-sidebar-right">

        <div class="uk-panel uk-panel-divider pk-panel-marginless">
            <ul class="uk-nav uk-nav-side" data-uk-tab="{ connect: '#tab-content' }">
                <li class="uk-active"><a href="#">@trans('Settings')</a></li>
                <li class=""><a href="#">@trans('Assignment')</a></li>
                @foreach (additionals as name => settings)
                <li class=""><a href="#">@name</a></li>
                @endforeach
            </ul>
        </div>

        <div class="uk-panel uk-panel-divider">
            <h3 class="uk-panel-title">Options</h3>

            <ul class="uk-list pk-list-table uk-margin-remove">

                <li>
                    <div>@trans('Status')</div>
                    <div>
                        <button class="uk-button uk-button-mini uk-button-danger js-status uk-hidden" type="button" data-status="0">@trans('Disabled')</button>
                        <button class="uk-button uk-button-mini uk-button-success js-status uk-hidden" type="button" data-status="1">@trans('Enabled')</button>
                        <input type="hidden" name="widget[status]" value="@widget.status">
                    </div>
                </li>

                <li>
                    <div>@trans('Position')</div>
                    <div>
                        <div class="uk-form-select" data-uk-form-select="{target:'button:first'}">
                            <button class="uk-button uk-button-mini" type="button">...</button>
                            <select id="form-position" name="widget[position]">
                                @foreach (positions as position)
                                <option value="@position.id"@(widget.position == position.id ? ' selected')>@trans(position.name)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </li>

                <li>
                    <div>@trans('Access')</div>
                    <div>
                        <div class="uk-form-select" data-uk-form-select="{target:'button:first'}">
                            <button class="uk-button uk-button-mini" type="button">...</button>
                            <select id="form-access" name="widget[accessId]">
                                @foreach (levels as level)
                                <option value="@level.id"@(widget.accessId == level.id ? ' selected')>@level.name</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </li>

                <li>
                    <div>@trans('Title')</div>
                    <div>
                        <button class="uk-button uk-button-mini js-title @(widget.get('show_title') ? 'uk-hidden':'')" type="button" data-value="0">@trans('Hide')</button>
                        <button class="uk-button uk-button-mini js-title @(!widget.get('show_title') ? 'uk-hidden':'')" type="button" data-value="1">@trans('Show')</button>
                        <input type="hidden" name="widget[settings][show_title]" value="@widget.get('show_title', '1')">
                    </div>
                </li>

            </ul>
        </div>

    </div>

    @token()

</form>