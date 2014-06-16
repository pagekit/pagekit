<div class="uk-form-row">
    <label class="uk-form-label" for="form-name">@trans('Name')</label>
    <div class="uk-form-controls">
        <input id="form-name" class="uk-width-1-1" name="name" value="@params.name" type="text" placeholder="@trans('Hello World')">
    </div>
</div>

<script>

    require(['jquery'], function($) {

        $('#form-name').on('change', function() {
            $('#form-url').val('@link?name=' + $(this).val());
        }).trigger('change');

    });

</script>