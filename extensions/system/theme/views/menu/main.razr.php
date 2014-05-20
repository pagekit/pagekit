<ul class="uk-grid uk-grid-width-1-3">
@foreach (root.children as item)
    <li@( item.attribute('active') ? ' class="uk-active"' )>
        <a href="@url.route(item.url)">
            <img src="@url.to(item.getIcon() ?: 'asset://system/images/icon-settings-extensions.svg')" width="50" height="50">
            <p>@trans(item)</p>
        </a>
    </li>
@endforeach
</ul>