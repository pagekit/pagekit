@foreach (widgets as widget)
<div class="uk-panel">
    @(widget.get('show_title', true) ? "<h3>" ~ widget.title ~ "</h3>")
    @provider.render(widget)
</div>
@endforeach