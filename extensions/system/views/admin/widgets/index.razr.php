@script('widgets', 'system/js/widgets.js', ['requirejs'])

<form id="js-widgets" class="uk-form" method="post" data-reorder="@url.route('@system/widgets/reorder')">

    <div class="pk-options uk-clearfix">
        <div class="uk-float-left">

            <div class="uk-button-dropdown" data-uk-dropdown="{ mode: 'click' }">
                <button class="uk-button uk-button-primary" type="button">@trans('Add Widget')</button>
                <div class="uk-dropdown uk-dropdown-small">
                    <ul class="uk-nav uk-nav-dropdown">
                        @foreach (app.widgets.types as type)
                        <li><a href="@url.route('@system/widgets/add', ['type' => type.id])">@type.name</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="uk-button-dropdown" data-uk-dropdown="{ mode: 'click' }">
                <button class="uk-button" type="button">@trans('Actions') <i class="uk-icon-caret-down"></i></button>
                <div class="uk-dropdown uk-dropdown-small">
                    <ul class="uk-nav uk-nav-dropdown">
                        <li><a href="#" data-action="@url.route('@system/widgets/enable')">@trans('Enable')</a></li>
                        <li><a href="#" data-action="@url.route('@system/widgets/disable')">@trans('Disable')</a></li>
                        <li class="uk-nav-divider"></li>
                        <li><a href="#" data-action="@url.route('@system/widgets/copy')">@trans('Copy')</a></li>
                        <li><a href="#" data-action="@url.route('@system/widgets/delete')">@trans('Delete')</a></li>
                    </ul>
                </div>
            </div>

        </div>
        <div class="uk-float-right">

            <input id="filter-title" type="text" name="filter[search]" placeholder="@trans('Search')" value="">

            <select id="filter-status" name="filter[status]" data-filter="status">
                <option value="">@trans('- Status -')</option>
                <option value="@constant('Pagekit\\Widget\\Model\\WidgetInterface::STATUS_ENABLED')">@trans('Enabled')</option>
                <option value="@constant('Pagekit\\Widget\\Model\\WidgetInterface::STATUS_DISABLED')">@trans('Disabled')</option>
            </select>

            <select id="filter-position" name="filter[position]" data-filter="position">
                <option value="">@trans('- Position -')</option>
                @foreach (positions|slice(0, positions|length - 1) as position)
                <option value="@position.id">@trans(position.name)</option>
                @endforeach
            </select>

            <select id="filter-type" name="filter[type]" data-filter="type">
                <option value="">@trans('- Type -')</option>
                @foreach (app.widgets.types as type)
                <option value="@type.id">@type.name</option>
                @endforeach
            </select>

        </div>
    </div>

    <div class="pk-table-fake pk-table-fake-header pk-table-fake-header-indent pk-table-fake-border">
        <div class="pk-table-width-minimum"><input type="checkbox" class="js-select-all"></div>
        <div>@trans('Title')</div>
        <div class="pk-table-width-100 uk-text-center">@trans('Status')</div>
        <div class="pk-table-width-150 uk-text-truncate">@trans('Position')</div>
        <div class="pk-table-width-150 uk-text-truncate">@trans('Type')</div>
        <div class="pk-table-width-100 uk-text-truncate">@trans('Access')</div>
    </div>

    @foreach (positions as position)
    <div class="js-position @(widgets[position.id]|length ? '' : 'uk-hidden')" data-position="@position.id">
        <div class="pk-table-fake pk-table-fake-header pk-table-fake-subheading">
            <div>
                @trans(position.name)
                @if (position.description)
                <span class="uk-text-muted">@trans(position.description)</span>
                @endif
            </div>
        </div>

        <ul class="uk-sortable" data-uk-sortable="{ maxDepth: 1 }" data-position="@position.id">
            @foreach (widgets[position.id] as widget)

            @set (type = app.widgets.types[widget.type])

            <li class="uk-form js-widget" data-id="@widget.id" data-status="@( widget.status ?: 0 )" data-type="@widget.type" data-title="@widget.title">

                <div class="uk-sortable-item pk-table-fake">
                    <div class="pk-table-width-minimum">
                        <div class="uk-sortable-handle">​</div>
                    </div>
                    <div class="pk-table-width-minimum"><input type="checkbox" name="ids[]" value="@widget.id"></div>
                    <div>
                        @if (type)
                        <a href="@url.route('@system/widgets/edit', ['id' => widget.id])">@widget.title</a>
                        @else
                        @widget.title
                        @endif
                    </div>
                    <div class="pk-table-width-100 uk-text-center">
                        <a class="uk-icon-circle uk-text-@( widget.status ? 'success' : 'danger' )" href="@url.route(widget.status ? '@system/widgets/disable' : '@system/widgets/enable', ['ids[]' => widget.id, '_csrf' => app.csrf.generate])"  title="@widget.statusText"></a>
                    </div>
                    <div class="pk-table-width-150">
                        <select name="positions[@widget.id]" class="uk-width-1-1">
                            @if (!position.id)
                            <option value="">@trans('- Assign -')</option>
                            @endif
                            @foreach (positions|slice(0, positions|length - 1) as position)
                            <option value="@position.id"@(position.id == widget.position ? ' selected')>@trans(position.name)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pk-table-width-150 uk-text-truncate">@(type.name ?: trans('Extension not loaded'))</div>
                    <div class="pk-table-width-100 uk-text-truncate">
                        @(levels[widget.accessId].name ?: trans('No access level'))
                    </div>
                </div>

            </li>
            @endforeach
        </ul>
    </div>
    @endforeach

    @token()

</form>