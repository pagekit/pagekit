<div class="uk-form-controls">
    <input type="text" class="uk-width-1-1" name="url" placeholder="@trans('Type custom URL')">
</div>

<script>

    require(['jquery', 'link'], function($, Link) {

        Link.register('', function(link, form) {

            var $url = $('[name="url"]', form)

                .on('change', function() {
                    link.set('', $url.val());
                });

            return {

                show: function(params, url) {
                    $url.val(url);
                },

                update: function() {
                    $url.trigger('change');
                }

            }

        });

    });

</script>