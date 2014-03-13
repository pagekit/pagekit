<div class="uk-form-controls">
    <input class="uk-width-1-1" name="name" value="" type="text" placeholder="@trans('Hello World')" required>
</div>

<script>

    (function($) {

        $(document).on('load.linkpicker', function(e, handler, params) {

            var $name = $('[name="name"]', handler.edit);

            $name.on('change', function() {
                    handler.updateUrl($name.serializeArray());
                })
                .val(params['name'] ? params['name'] : '')
                .trigger('change');
        });

    })(jQuery);

</script>