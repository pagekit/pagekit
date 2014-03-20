<script>

    (function($) {

        $('.js-edit').on('change.linkpicker', function(e, params, url) {

            if (url !== '/') return;

            $(this).trigger('update.linkpicker');

        });

    })(jQuery);

</script>