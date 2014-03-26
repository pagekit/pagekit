<script>

    require(['link'], function(Link) {

        Link.register('@route', function(link, form) {

            return {
                show: function(params, url) {
                    link.set();
                }
            }

        });

    });

</script>