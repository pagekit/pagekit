@if (root.depth == 0)
<ul class="uk-nestable js-menu-items" data-uk-nestable>
@endif

    @foreach (root.children as menuitem)

    @set (item = menuitem.item)

    <li class="uk-nestable-list-item" data-id="@item.id">

        <div class="uk-nestable-item pk-table-fake">
            <div class="pk-table-width-minimum"><div class="uk-nestable-handle"></div></div>
            <div class="pk-table-width-minimum pk-padding-horizontal-remove"><div data-nestable-action="toggle"></div></div>
            <div class="pk-table-width-minimum"><input class="js-select" type="checkbox" name="id[]" value="@item.id"></div>
            <div class="pk-table-min-width-100">
                <a href="@url.route('@system/item/edit', ['id' => item.id])">@item.name</a>
            </div>
            <div class="pk-table-width-100 uk-text-center">
                <a class="uk-icon-circle uk-text-@(item.status ? 'success' : 'danger')" href="#" data-action="@url.route('@system/item/status', ['menu' => item.menuId,'id' => item.id, 'status' => item.status ? '0' : '1'])" title="@item.statusText"></a>
            </div>
            <div class="pk-table-width-150 pk-table-max-width-150 uk-text-truncate">
                @if (item.url == '!divider')
                @trans('Divider')
                @elseif (item.url == '!menu-header')
                @trans('Menu Header')
                @else
                <a href="@url.route(item.url)" target="_blank">@(url.route(item.url, [], 'base')|urldecode ?: '/')</a>
                @endif
            </div>
            <div class="pk-table-width-100">
                @(levels[item.accessId].name ?: trans('No access level'))
            </div>
        </div>

        @if (menuitem.hasChildren())
        <ul class="uk-nestable-list">
            @include('view://system/admin/menu/item.razr.php', ['menu' => menu, 'root' => menuitem, 'levels' => levels])
        </ul>
        @endif

    </li>

    @endforeach

@if (root.depth == 0)
</ul>
@endif