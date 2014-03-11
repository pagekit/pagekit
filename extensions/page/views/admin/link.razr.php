<div class="uk-form-controls">
    <select class="uk-width-1-1" name="id">
        @foreach (pages as page)
        <option value="@page.id">@page.title</option>
        @endforeach
    </select>
</div>

<script>

    (function($) {

        var handler, $id = $('[name=id]', '.js-link-edit');

        $(document).on('load.urlpicker', function(e, link, params) {
            handler = link;

            $id.val($('option[value="'+params['id']+'"]', $id).length ? params['id'] : $('option:first', $id).val()).trigger('change');
        });

        $id.on('change', function() {
            handler.updateUrl($id.serializeArray());
        });

    })(jQuery);

</script>