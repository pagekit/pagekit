<div class="uk-form-controls">
    <input class="uk-width-1-1" name="name" value="" type="text" placeholder="@trans('Hello World')">
</div>

<script>

    require(['jquery', 'link'], function($, Link) {

        Link.register('@route', function(link, form) {

            var $name = $('[name="name"]', form)

                .on('change', function() {
                    link.set($name.serialize());
                });

            return {
                show: function(params, url) {
                    $name.val(params['name'] ? params['name'] : '').trigger('change');
                }
            }

        });

    });

</script>