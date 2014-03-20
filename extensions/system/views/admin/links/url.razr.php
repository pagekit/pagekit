<div class="uk-form-controls">
    <input type="text" class="uk-width-1-1" name="url" placeholder="@trans('Type custom URL')">
</div>

<script>

    (function($) {

        var $edit = $('.js-edit'), $url = $('[name="url"]', $edit);

        $edit.on('load.linkpicker', function(e, params, url, type) {

            if (type != '') return;

            $url.val(url).trigger('change');
        });

        $url.on('change', function() {
            $edit.trigger('update.linkpicker', [null, $url.val()]);
        });

    })(jQuery);

</script>