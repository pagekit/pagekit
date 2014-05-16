@if (root.depth == 0)
<ul class="uk-nav @(classes ?! 'uk-nav-side')@(options.classes ? ' '~options.classes : '')">
@endif

@foreach (root.children as item)

    @set (header = item.url == '!menu-header', divider = item.url == '!divider')

    <li class="@((item.attribute('parent') ? ' uk-parent')~(item.attribute('active') ? ' uk-active')~(header ? ' uk-nav-header')~(divider ? ' uk-nav-divider')|trim)">

        @if (header)
        @item
        @elseif (!divider)
        <a href="@url.route(item.url)">@item</a>
        @endif

        @if (item.hasChildren && (item.attribute('active') || widget.get('mode', 'all') == 'all' || !root.depth == 0))
        <ul class="uk-nav-sub">
            @include('view://system/widgets/menu/nav.razr.php', ['root' => item])
        </ul>
        @endif
    </li>

@endforeach

@if (root.depth == 0)
</ul>
@endif