@if (root.children)
@if (root.depth == 1)
<ul class="uk-subnav tm-subnav">
@endif

@foreach (root.children as item)

    <li@( item.attribute('active') ? ' class="uk-active"' )>
        <a href="@url.route(item.url)">@trans(item)</a>
    </li>

@endforeach

@if (root.depth == 1)
</ul>
@endif
@endif