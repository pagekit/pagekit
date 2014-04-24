@foreach (widgets as widget)
    @if (widget.type == 'widget.menu')

    @provider.render(widget, ['layout' => 'view://system/widgets/menu/style.offcanvas.razr.php']|merge(options))

    @else

    @include('theme://alpha/views/renderer/position.panel.razr.php')

    @endif
@endforeach