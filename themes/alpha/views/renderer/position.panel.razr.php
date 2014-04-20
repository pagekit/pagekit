@foreach (widgets as widget)
<div class="uk-panel @widget.settings.theme.panel @if (widget.settings.theme.alignment)uk-text-center@endif">

    @if (widget.settings.theme.badge.text)
    <div class="@widget.settings.theme.badge.type">@widget.settings.theme.badge.text</div>
    @endif

    @(widget.showTitle ? "<h3>" ~ widget.title ~ "</h3>")
    @provider.render(widget, options)
</div>
@endforeach