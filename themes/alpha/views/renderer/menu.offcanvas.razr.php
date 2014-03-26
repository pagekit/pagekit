@if (root.depth == 0)
<ul class="uk-nav uk-nav-offcanvas" data-uk-nav>
@endif

@foreach (root.children as item)

    @set (divider = item.url == '!divider', header = item.url == '!menu-header', active = item.attribute('active'), parent = item.hasChildren())

    <li@(active || header || divider || parent ? ' class="'~(((active ? 'uk-active')~(header ? ' uk-nav-header')~(divider ? ' uk-nav-divider')~(parent ? ' uk-parent'))|trim)~'"')>

        @if (header)
        @item.item.name
        @elseif (!divider)
        <a href="@url.route(item.url)">@item</a>
        @endif

        @if (parent)
        <ul class="uk-nav-sub">
            @include('theme://alpha/views/renderer/menu.offcanvas.razr.php', ['root' => item])
        </ul>
        @endif
    </li>

@endforeach

@if (root.depth == 0)
</ul>
@endif