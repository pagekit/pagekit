<div class="uk-form-row">
    <label class="uk-form-label" for="form-view">@trans('View')</label>
    <div class="uk-form-controls">
        <select id="form-view" class="uk-width-1-1" name="id">
            @foreach (routes as id => route)
            <option value="@id"@(params.id == id ? ' selected')>@route</option>
            @endforeach
        </select>
    </div>
</div>

<script>

    require(['jquery'], function($) {

        $('#form-view').on('change', function() {
            $('#form-url').val($(this).val());
        }).trigger('change');

    });

</script>