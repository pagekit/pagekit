<ul class="uk-navbar-nav">
    <li@( root.attribute('active') ? ' class="uk-active"' )>
        <a href="@url.route(root.url)">@trans(root)</a>
    </li>
    @foreach (root.children as item)
    <li@( item.attribute('active') ? ' class="uk-active"' )>
        <a href="@url.route(item.url)">@trans(item)</a>
    </li>
    @endforeach
</ul>