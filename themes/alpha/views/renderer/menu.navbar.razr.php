@if (root.depth == 0)
<ul class="uk-navbar-nav">
@endif

@foreach (root.children as item)

    <li class="@(item.attribute('active') ? 'uk-active' )@( item.hasChildren() ? ' uk-parent')"@( item.hasChildren() && root.depth == 0 ? ' data-uk-dropdown' )>
        <a href="@url(item.url)">@item</a>
        @if (item.hasChildren())
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