@if (root.depth == 0)
<ul @block('menuAttributes')@endblock>
@endif

@foreach (root.children as item)

    <li@block('itemAttributes')@( item.attribute('active') ? ' class="uk-active"' )@endblock>
        <a href="@url.to(item.url)">@item</a>

        @if (item.hasChildren())
        <ul>
            @block('recursion')
            @include('view://system/widgets/menu/style.base.razr.php', ['root' => item])
            @endblock
        </ul>
        @endif
    </li>

@endforeach

@if (root.depth == 0)
</ul>
@endif