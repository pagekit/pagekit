<div class="uk-form-controls">
    <input class="uk-width-1-1" name="name" value="" type="text" placeholder="@trans('Hello World')" required>
</div>

<script>

    (function($) {

        var handler, $name = $('[name=name]');

        $(document).on('load.urlpicker', function(e, link, params) {
            handler = link;
            $name.val(params['name'] ? params['name'] : '').trigger('change');
        });

        $name.on('change', function() {
            handler.updateUrl($name.serializeArray());
        });

    })(jQuery);

</script>