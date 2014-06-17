<div class="uk-form-row">
    <label for="form2-view" class="uk-form-label">@trans('View')</label>
    <div class="uk-form-controls">
        <select id="form2-view" class="uk-width-1-1" name="id">
            @foreach (routes as id => route)
            <option value="@id"@(params.id == id ? ' selected')>@route</option>
            @endforeach
        </select>
    </div>
</div>

<script>

    require(['jquery'], function($) {

        $('.js-linkpicker #form-view').on('change', function() {
            $('.js-linkpicker #form-url').val($(this).val());
        }).trigger('change');

    });

</script>