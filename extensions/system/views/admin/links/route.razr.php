<script>

    require(['link'], function(Link) {

        Link.register('@route', function(link, form) {

            link.set();

            return {
                show: function(params, url) {
                    link.set();
                }
            }

        });

    });

</script>