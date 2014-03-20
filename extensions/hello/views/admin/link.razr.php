<div class="uk-form-controls">
    <input class="uk-width-1-1" name="name" value="" type="text" placeholder="@trans('Hello World')">
</div>

<script>

    (function($) {

        var $edit = $('.js-edit'), $name = $('[name="name"]', $edit);

        $edit.on('change.linkpicker', function(e, params, url, type) {

            if (type !== '@route') return;

            $name.val(params['name'] ? params['name'] : '').trigger('change');
        });

        $name.on('change', function() {
            $edit.trigger('update.linkpicker', $name.serialize());
        });

    })(jQuery);

</script>