@foreach (widgets as widget)
<div class="uk-panel">
    @(widget.showTitle ? "<h3>" ~ widget.title ~ "</h3>")
    @provider.render(widget, options)
</div>
@endforeach