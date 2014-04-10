require(['jquery','uikit!pagination' , 'domReady!'], function($, uikit) {

    var form = $('#js-pages'), table = $('.js-table', form), pagination = $('[data-uk-pagination]', form), search, prev;

    // action button
    form.on('click', '[data-action]', function(e) {
        e.preventDefault();

        $.post($(this).data('action'), form.serialize(), function(response) {
            uikit.notify(response.message, response.error ? 'danger' : 'success');
            updateTable();
        });
    })

    // select all checkbox
    .on('click', '.js-select-all:checkbox', function() {
        $('[name="ids[]"]:checkbox', form).prop('checked', $(this).prop('checked'));
    })

    // submit filters
    .on('keyup', 'input[name^="filter"]', uikit.Utils.debounce(function() {
        updateTable();
    }, 200))
    .on('change', 'select[name^="filter"]', function() {
        updateTable();
    })
    .on('submit', function(e) {
        e.preventDefault();
    });

    // pagination
    pagination.on('uk-select-page', function(e, index) {
        $('[name="page"]', form).val(index);
        updateTable();
    });

    function updateTable() {
        $.post(form.data('action'), form.serialize(), function(response) {
            table.html(response.table);
            pagination.toggleClass('uk-hidden', response.table === '').data('pagination').render(response.total);
            $('.uk-alert', form).toggleClass('uk-hidden', response.table !== '');
        });
    }

});