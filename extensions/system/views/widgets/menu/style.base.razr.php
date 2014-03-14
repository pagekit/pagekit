@if (root.depth == widget.get('start_level', 1) -1)
<ul @block('menuAttributes')@endblock>
@endif

@foreach (root.children as item)

    <li@block('itemAttributes')@(item.attribute('active') ? ' class="uk-active"')@endblock>
        <a href="@url.route(item.url)">@item</a>

        @item.setAttribute('show_children', ((root.item && root.attribute('show_children')) || item.attribute('active') || widget.get('mode', 'all') == 'all')
            && (!widget.get('depth') || (widget.get('start_level', 1) + widget.get('depth') - 1) > item.depth))

        @if (item.hasChildren() && item.attribute('show_children'))
        <ul>
            @block('recursion')
            @include('view://system/widgets/menu/style.base.razr.php', ['root' => item])
            @endblock
        </ul>
        @endif
    </li>

@endforeach

@if (root.depth == widget.get('start_level', 1) -1)
</ul>
@endif