@if (root.depth == 0)
<ul class="uk-navbar-nav">
@endif

@foreach (root.children as item)

    @set (divider = item.url == '!divider', header = item.url == '!menu-header', active = item.attribute('active'), parent = item.hasChildren())

    <li@(active || header || divider || parent ? ' class="'~(((active ? 'uk-active')~(header ? ' uk-nav-header')~(divider ? ' uk-nav-divider')~(parent ? ' uk-parent'))|trim)~'"')@(parent && root.depth == 0 ? ' data-uk-dropdown')>

        @if (header)
        @item.item.name
        @elseif (!divider)
        <a href="@url.route(item.url)">@item</a>
        @endif

        @if (parent)
        @if (root.depth == 0)
        <div class="uk-dropdown uk-dropdown-navbar">
            <ul class="uk-nav uk-nav-navbar">
                @include('theme://alpha/views/renderer/menu.navbar.razr.php', ['root' => item])
            </ul>
        </div>
        @else
        <ul class="uk-nav-sub">
            @include('theme://alpha/views/renderer/menu.navbar.razr.php', ['root' => item])
        </ul>
        @endif
        @endif
    </li>

@endforeach

@if (root.depth == 0)
</ul>
@endif