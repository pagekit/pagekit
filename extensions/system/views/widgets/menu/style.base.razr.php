@if (root.depth == widget.get('start_level', 1) -1)
<ul @block('menuAttributes')@endblock>
@endif

@foreach (root.children as item)

    @set (divider = item.url == '!divider', header = item.url == '!menu-header', active = item.attribute('active'), hasChildren = item.hasChildren())

    <li@block('itemAttributes')@(active || header || divider ? ' class="'~(((active ? 'uk-active')~(header ? ' uk-nav-header')~(divider ? ' uk-nav-divider'))|trim)~'"')@endblock>

        @if (header)
        @item.item.name
        @elseif (!divider)
        <a href="@url.route(item.url)">@item</a>
        @endif

        @item.setAttribute('show_children', ((root.item && root.attribute('show_children')) || active || widget.get('mode', 'all') == 'all')
            && (!widget.get('depth') || (widget.get('start_level', 1) + widget.get('depth') - 1) > item.depth))

        @if (hasChildren && item.attribute('show_children'))
        @block('childrenStart')<ul>@endblock
            @block('recursion')
            @include('view://system/widgets/menu/style.base.razr.php', ['root' => item])
            @endblock
        @block('childrenEnd')</ul>@endblock
        @endif
    </li>

@endforeach

@if (root.depth == widget.get('start_level', 1) -1)
</ul>
@endif