@script('widget', 'system/js/widgets/edit.js', 'requirejs')

<form class="js-widget uk-form uk-form-stacked" action="@url.route('@system/widgets/save', ['id' => widget.id ?: 0])" method="post">

    <div class="pk-toolbar">
        <button class="uk-button uk-button-primary" type="submit">@trans('Save')</button>
        <a class="uk-button" href="@url.route('@system/widgets/index')">@(widget.id ? trans('Close') : trans('Cancel'))</a>
    </div>

    <div class="uk-grid uk-grid-divider" data-uk-grid-margin data-uk-grid-match>
        <div class="uk-width-medium-3-4">

            <ul class="uk-tab" data-uk-tab="{connect:'#tab-content'}">
                <li><a href="#">@trans('Settings')</a></li>
                <li><a href="#">@trans('Assignment')</a></li>
                @foreach (additionals as name => settings)
                <li><a href="#">@name</a></li>
                @endforeach
            </ul>

            <ul id="tab-content" class="uk-switcher uk-margin">
                <li>

                    <div class="uk-form-row">
                        <label for="form-title" class="uk-form-label">@trans('Title')</label>
                        <div class="uk-form-controls">
                            <p class="uk-form-controls-condensed">
                                <input id="form-title" class="uk-width-1-1 uk-form-large" type="text" name="widget[title]" value="@widget.title" required>
                            </p>
                        </div>
                    </div>

                    @set (type = app.widgets.types[widget.type])
                    @type.renderForm(widget)

                </li>
                <li>

                    @foreach (app.menus as id => menu)
                    @if (menu.items)
                    <div class="uk-form-row">
                        <label for="form-h-it" class="uk-form-label">@menu.name @trans('Menu')</label>
                        <div class="uk-form-controls uk-form-controls-text">
                        @include('view://system/admin/widgets/select.razr.php', ['root' => app.menus.getTree(menu), 'widget' => widget])
                        </div>
                    </div>
                    @endif
                    @endforeach

                    <div class="uk-form-row">
                        <label for="form-pages" class="uk-form-label">@trans('Pattern')</label>
                        <div class="uk-form-controls">
                            <textarea id="form-pages" class="uk-form-width-large" name="widget[pages]" rows="5">@widget.pages</textarea>
                            <p class="uk-form-help-block">@trans('Enter one page path per line. The "*" character is a wildcard. Exclude pages by prepending an exclamation mark to the path. Example paths: "blog" for the blog page, "blog/*" for blog entries or "!blog" if you want to exclude the blog.')</p>
                        </div>
                    </div>

                    <input type="hidden" name="widget[menuItems][]" value="">

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
            <div class="uk-panel uk-panel-divider">

                <div class="uk-form-row">
                    <label class="uk-form-label">@trans('Status')</label>
                    <div class="uk-form-controls">
                        <select class="uk-width-1-1" name="widget[status]">
                            <option value="1"@(widget.status ? ' selected')>@trans('Enabled')</option>
                            <option value="0"@(!widget.status ? ' selected')>@trans('Disabled')</option>
                        </select>
                    </div>
                </div>
                <div class="uk-form-row">
                    <label class="uk-form-label">@trans('Access')</label>
                    <div class="uk-form-controls">
                        <select class="uk-width-1-1" name="widget[accessId]">
                            @foreach (levels as level)
                            <option value="@level.id"@(widget.accessId == level.id ? ' selected')>@level.name</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="uk-form-row">
                    <label class="uk-form-label">@trans('Position')</label>
                    <div class="uk-form-controls">
                        <select class="uk-width-1-1" name="widget[position]">
                            @foreach (positions as position)
                            <option value="@position.id"@(widget.position == position.id ? ' selected')>@trans(position.name)</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="uk-form-row">
                    <label class="uk-form-label">@trans('Options')</label>
                    <div class="uk-form-controls">
                        <label><input type="checkbox" name="widget[settings][show_title]" value="1"@(widget.get('show_title', 1) ? ' checked')> @trans('Show Title')</label>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @token()

</form>