<div class="uk-form-row">
    <label class="uk-form-label" for="form-page">@trans('Page')</label>
    <div class="uk-form-controls">
        <select id="form-page" class="uk-width-1-1" name="id">
            @foreach (pages as page)
            <option value="@page.id"@(params.id == page.id ? ' selected')>@page.title</option>
            @endforeach
        </select>
    </div>
</div>

<script>

    require(['jquery'], function($) {

        $('#form-page').on('change', function() {
            $('#form-url').val('@link?id=' + $(this).val());
        }).trigger('change');

    });

</script>