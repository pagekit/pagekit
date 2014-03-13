<div class="uk-form-controls">
    <input type="text" class="uk-width-1-1" name="url" placeholder="@trans('Type custom URL')">
</div>

<script>

    (function($) {

        $(document).on('load.urlpicker', function(e, handler, params, url) {

            var $url = $('[name=url]', handler.edit).on('change', handler.edit, function() {
                handler.updateUrl(null, $url.val());
            }).val(url);
        });

    })(jQuery);

</script>