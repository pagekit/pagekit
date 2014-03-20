<script>

    (function($) {

        $('.js-edit').on('load.linkpicker', function(e, params, url, type) {

            if (type != '@@frontpage') return;

            $(this).trigger('update.linkpicker');

        });

    })(jQuery);

</script>