@if (root.depth == 0)
<ul class="uk-nav uk-nav-offcanvas">
@endif

@foreach (root.children as item)
    <li@(item.attribute('active') ? ' class="uk-active"')>
        <a href="@url.route(item.url)">@trans(item)</a>
    </li>
@endforeach

@if (root.depth == 0)
</ul>
@endif