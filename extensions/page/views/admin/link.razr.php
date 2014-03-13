<div class="uk-form-controls">
    <select class="uk-width-1-1" name="id">
        @foreach (pages as page)
        <option value="@page.id">@page.title</option>
        @endforeach
    </select>
</div>

<script>

    (function($) {

        $(document).on('load.urlpicker', function(e, handler, params) {

            var $id = $('[name=id]', handler.edit);

            $id
                .on('change', function() {
                    handler.updateUrl($id.serializeArray());
                })
                .val($('option[value="'+params['id']+'"]', $id).length ? params['id'] : $('option:first', $id).val())
                .trigger('change');
        });

    })(jQuery);

</script>