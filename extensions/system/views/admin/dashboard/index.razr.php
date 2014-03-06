@style('system', 'system/css/system.css')

<div class="uk-grid pk-grid-small uk-grid-preserve" data-uk-grid-margin>

    @foreach(columns as column)
    <div class="uk-width-medium-1-3">
        @foreach(column as id)
        <div class="uk-panel uk-panel-box">
            @widgets[id]
        </div>
        @endforeach
    </div>
    @endforeach

</div>