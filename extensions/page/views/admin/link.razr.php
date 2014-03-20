<div class="uk-form-controls">
    <select class="uk-width-1-1" name="id">
        @foreach (pages as page)
        <option value="@page.id">@page.title</option>
        @endforeach
    </select>
</div>

<script>

    (function($) {

        var $edit = $('.js-edit'), $id = $('[name="id"]', $edit);

        $edit.on('load.linkpicker', function(e, params, url, type) {
            if (type !== '@route') return;

            $id.val($('option[value="'+params['id']+'"]', $id).length ? params['id'] : $('option:first', $id).val()).trigger('change');
        });

        $id.on('change', function() {
            $edit.trigger('update.linkpicker', $id.serialize());
        });

    })(jQuery);

</script>