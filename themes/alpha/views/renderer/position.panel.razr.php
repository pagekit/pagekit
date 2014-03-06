@foreach (widgets as widget)
<div class="uk-panel">
    @( widget.settings.show_title !== false ? "<h3>" ~ widget.title ~ "</h3>")
    @provider.render(widget)
</div>
@endforeach