@if (root.depth == 0)
<ul @block('menuAttributes')@endblock>
@endif

@foreach (root.children as item)

    <li@( item.attribute('active') ? ' class="uk-active"' )>
        <a href="@url(item.url)">@trans(item)</a>
    </li>

@endforeach

@if (root.depth == 0)
</ul>
@endif