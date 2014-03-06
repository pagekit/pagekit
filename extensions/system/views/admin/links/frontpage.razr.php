<script>

    (function($) {

        $(document).on('load.urlpicker', function(e, link, params) {
            link.updateUrl();
        });

    })(jQuery);

</script>