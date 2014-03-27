@if (root.depth == 0)
<ul@block('menuAttributes')@endblock>
@endif

@foreach (root.children as item)

    @set (header = item.url == '!menu-header', divider = item.url == '!divider')

    <li@block('itemAttributes')@endblock>

        @if (header)
        @item
        @elseif (!divider)
        <a href="@url.route(item.url)">@item</a>
        @endif

        @if (item.hasChildren && (item.attribute('active') || widget.get('mode', 'all') == 'all' || !root.depth == 0))
        @block('children')@endblock
        @endif
    </li>

@endforeach

@if (root.depth == 0)
</ul>
@endif