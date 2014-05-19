@foreach (widgets as widget)
    @if (widget.type == 'widget.menu')

    @provider.render(widget, ['layout' => 'view://system/widgets/menu/nav.razr.php', 'classes' => 'uk-nav-offcanvas']|merge(options))

    @else

    @include('theme://alpha/views/renderer/position.panel.razr.php', ['widgets' => [widget]])

    @endif
@endforeach