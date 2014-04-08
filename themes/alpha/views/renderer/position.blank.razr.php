@foreach (widgets as widget)
    @provider.render(widget, options)
@endforeach