@script('dashboard', 'system/js/dashboard/settings.js', 'requirejs')

<form id="js-dashboard" class="uk-form" action="@url.route('@system/system/admin')" method="post" data-reorder="@url.route('@system/dashboard/reorder')">

    <div class="pk-options uk-clearfix">
        <div class="uk-float-left">

            <div class="uk-button-dropdown" data-uk-dropdown="{ mode: 'click' }">
                <button class="uk-button uk-button-primary" type="button">@trans('Add Widget')</button>
                <div class="uk-dropdown uk-dropdown-small">
                    <ul class="uk-nav uk-nav-dropdown">
                        @foreach (types as type)
                        <li><a href="@url.route('@system/dashboard/add', ['type' => type.id])">@type.name</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <a class="uk-button uk-hidden js-show-on-select" href="#" data-action="@url.route('@system/dashboard/delete')">@trans('Delete')</a>

        </div>
    </div>

    <div class="pk-table-fake pk-table-fake-header pk-table-fake-header-indent">
        <div class="pk-table-width-minimum"><input type="checkbox" class="js-select-all"></div>
        <div>@trans('Widget')</div>
        <div class="pk-table-width-100">@trans('Type')</div>
    </div>

    <ul class="uk-sortable" data-uk-sortable="{ maxDepth: 1 }">
        @foreach (widgets as id => widget)
        <li data-id="@id">

            <div class="uk-sortable-item pk-table-fake">
                <div class="pk-table-width-minimum"><div class="uk-sortable-handle">​</div></div>
                <div class="pk-table-width-minimum"><input class="js-select" type="checkbox" name="ids[]" value="@id"></div>
                <div>
                    <a href="@url.route('@system/dashboard/edit', ['id' => id])">@widget.title</a>
                </div>
                <div class="pk-table-width-100">@widget.type</div>
            </div>

        </li>
        @endforeach
    </ul>

    @token()

</form>