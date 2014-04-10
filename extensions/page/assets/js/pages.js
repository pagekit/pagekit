require(['jquery','uikit!pagination' , 'domReady!'], function($, uikit) {

    var form = $('#js-pages'), page = $('[name="page"]', form), rows = $('.js-rows', form), pagination = $('[data-uk-pagination]', form);

    form

        // action button
        .on('click', '[data-action]', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            $.post($(this).data('action'), form.serialize(), function(response) {
                uikit.notify(response.message, response.error ? 'danger' : 'success');
                page.trigger('change');
            });
        })

        // submit filters
        .on('change', 'select[name^="filter"]', function() {
            form.submit();
        })

        // select all checkbox
        .on('click', '.js-select-all:checkbox', function() {
            $('[name="ids[]"]:checkbox', form).prop('checked', $(this).prop('checked'));
        });

        // pagination
        pagination.on('uk-select-page', function(e, index) {
            page.val(index).trigger('change');
        });

        // refresh rows
        page.on('change', function(e) {

            var values = form.serializeArray();
            values.push({ name: 'rows', value: true });

            $.post($(this).data('action'), $.param(values), function(response) {

                rows.html(response.rows);
                pagination.data('pagination').render(response.total);

                $('.js-select-all:checkbox', form).prop('checked', false);
            });
        });
});