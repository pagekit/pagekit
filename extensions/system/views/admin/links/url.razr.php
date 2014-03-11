<div class="uk-form-controls">
    <input type="text" class="uk-width-1-1" name="url" placeholder="@trans('Type custom URL')">
</div>

<script>

    (function($) {

        var handler, $url = $('[name=url]', '.js-link-edit');

        $(document).on('load.urlpicker', function(e, link, params, url) {
            handler = link;
            $url.val(url);
        });

        $url.on('change', function() {
            handler.updateUrl(null, $url.val());
        });

    })(jQuery);

</script>