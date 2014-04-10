require(['jquery','uikit!pagination' , 'domReady!'], function($, uikit) {

    var form       = $('#js-pages'),
        page       = $('[name="page"]', form),
        rows       = $('.js-rows', form),
        pagination = $('[data-uk-pagination]', form);

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
        .on('change', '[name^="filter"]', function(e) {
            page.trigger('change');
        })

        // select all checkbox
        .on('click', '.js-select-all:checkbox', function() {
            $('[name="ids[]"]:checkbox', form).prop('checked', $(this).prop('checked'));
        })
        .on('submit', function(e) {
            e.preventDefault();
        });

    // pagination
    pagination.on('uk-select-page', function(e, index) {
        page.val(index).trigger('change');
    });

    // refresh rows
    page.on('change', function(e) {
        $.post($(this).data('action'), form.serialize(), function(response) {

            rows.html(response.rows);
            pagination.data('pagination').render(response.total);

            $('.js-select-all:checkbox', form).prop('checked', false);
            refreshView();
        });
    });

    function refreshView() {
        var empty = !rows.children().length;

        $('.js-not-empty', form).toggleClass('uk-hidden', empty);
        $('.js-empty', form).toggleClass('uk-hidden', !empty);
    }
    refreshView();
});