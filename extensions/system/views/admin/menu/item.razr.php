@if (root.depth == 0)
<ul class="uk-sortable" data-uk-sortable>
@endif

    @foreach (root.children as menuitem)

    @set (item = menuitem.item)

    <li class="uk-sortable-list-item" data-id="@item.id">

        <div class="uk-sortable-item pk-table-fake">
            <div class="pk-table-width-minimum"><div class="uk-sortable-handle"></div></div>
            <div class="pk-table-width-minimum pk-padding-horizontal-remove"><div data-sortable-action="toggle"></div></div>
            <div class="pk-table-width-minimum"><input type="checkbox" name="id[]" value="@item.id"></div>
            <div>
                <a href="@url.route('@system/item/edit', ['id' => item.id])">@item.name</a>
            </div>
            <div class="pk-table-width-100 uk-text-center">
                <a class="uk-icon-circle uk-text-@(item.status ? 'success' : 'danger')" href="#" data-action="@url.route('@system/item/status', ['menu' => item.menuId,'id' => item.id, 'status' => item.status ? '0' : '1'])" title="@item.statusText"></a>
            </div>
            <div class="pk-table-width-200 uk-text-truncate">
                <a href="@url.route(item.url)" target="_blank">@(url.route(item.url, [], 'base') ?: '/')</a>
            </div>
            <div class="pk-table-width-100">
                @(levels[item.accessId].name ?: trans('No access level'))
            </div>
        </div>

        @if (menuitem.hasChildren())
        <ul class="uk-sortable-list">
            @include('view://system/admin/menu/item.razr.php', ['menu' => menu, 'root' => menuitem, 'levels' => levels])
        </ul>
        @endif

    </li>

    @endforeach

@if (root.depth == 0)
</ul>
@endif