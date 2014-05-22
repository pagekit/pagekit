<ul class="uk-sortable uk-grid uk-grid-width-1-3 js-admin-menu" data-url="@url.route('@system/system/adminmenu')" data-uk-sortable>
@foreach (root.children as item)
    <li@( item.attribute('active') ? ' class="uk-active"' ) data-id="@item.getId()">
        <a class="uk-panel pk-panel-icon" href="@url.route(item.url)">
            <img src="@url.to(item.getIcon() ?: 'asset://system/images/icon-settings-extensions.svg')" width="50" height="50">
            <p>@trans(item)</p>
        </a>
    </li>
@endforeach
</ul>