<script>

    require(['link'], function(Link) {

        Link.register('@route', function(link, form) {

            return {

                show: function(params, url) {

                },

                update: function() {
                    link.set();
                }

            }

        });

    });

</script>