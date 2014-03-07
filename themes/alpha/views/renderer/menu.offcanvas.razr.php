@if (root.depth == 0)
<ul class="uk-nav uk-nav-offcanvas" data-uk-nav>
@endif

@foreach (root.children as item)

    <li class="@(item.attribute('active') ? 'uk-active')@(item.hasChildren() ? ' uk-parent')">
        <a href="@url(item.url)">@item</a>

        @if (item.hasChildren())
        <ul class="uk-nav-sub">
            @include('theme://alpha/views/renderer/menu.offcanvas.razr.php', ['root' => item])
        </ul>
        @endif
    </li>

@endforeach

@if (root.depth == 0)
</ul>
@endif