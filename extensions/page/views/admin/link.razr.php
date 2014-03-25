<div class="uk-form-controls">
    <select class="uk-width-1-1" name="id">
        @foreach (pages as page)
        <option value="@page.id">@page.title</option>
        @endforeach
    </select>
</div>

<script>

    require(['jquery', 'link'], function($, Link) {

        Link.register('@route', function(link, form) {

            var $id = $('[name="id"]', form);

                $id.on('change', function() {
                    link.set($id.serialize());
                });

            return {
                show: function(params, url) {
                    $id.val($('option[value="'+params['id']+'"]', $id).length ? params['id'] : $('option:first', $id).val()).trigger('change');
                }
            }

        });

    });

</script>