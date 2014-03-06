@if (root.depth == 0)
<ul class="uk-list uk-margin-top-remove">
@endif

    @foreach (root.children as node)

    @set(item = node.item)
    @set(type = app.menus.type(item.type))

    <li>

        <label>
            <input type="checkbox" name="widget[menuItems][]" value="@item.id"@( widget.hasMenuItem(item.id) ? ' checked' )>
            @item.name
        </label>

        @if (node.hasChildren())
        <ul class="uk-list">
            @include('view://system/admin/widgets/select.razr.php', ['root' => node, 'widget' => widget])
        </ul>
        @endif

    </li>

    @endforeach

@if (root.depth == 0)
</ul>
@endif