@foreach (widgets as widget)
    @if (widget.type == 'widget.menu')

    @provider.render(widget, ['layout' => 'view://system/widgets/menu/navbar.razr.php']|merge(options))

    @else

    <ul class="uk-navbar-nav uk-visible-large">
        <li class="uk-parent" data-uk-dropdown>
            <a href="#">@widget.title</a>
            <div class="uk-dropdown uk-dropdown-navbar">@provider.render(widget, options)</div>
        </li>
    </ul>
    @endif
@endforeach