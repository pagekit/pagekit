<ul class="uk-subnav @(options.classes ? ' '~options.classes : '')">
@foreach (root.children as item)
    <li class="@(item.attribute('active') ? ' uk-active')|trim">
        <a href="@url.route(item.url)">@item</a>
    </li>
@endforeach
</ul>