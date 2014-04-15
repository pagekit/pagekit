require(['jquery','uikit!pagination', 'rowselect', 'domReady!'], function($, uikit, RowSelect) {

    var form         = $('#js-pages'),
        showOnSelect = form.find('.js-show-on-select').addClass('uk-hidden'),
        table        = $('.js-table', form).on('selected-rows', function(e, rows) { showOnSelect[rows.length ? 'removeClass':'addClass']('uk-hidden'); }),
        rowselect    = new RowSelect(table),
        pagination   = $('[data-uk-pagination]', form),
        search, prev;

    // action button
    form.on('click', '[data-action]', function(e) {
        e.preventDefault();

        $.post($(this).data('action'), form.serialize(), function(data) {
            uikit.notify(data.message, data.error ? 'danger' : 'success');
            updateTable();
            updateOnSelect();
        });
    })

    // select all checkbox
    .on('click', '.js-select-all:checkbox', function() {
        $('.js-select', form).prop('checked', $(this).prop('checked'));
        rowselect.handleSelected();
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
        $.post(form.data('action'), form.serialize(), function(data) {
            table.html(data.table);
            pagination.toggleClass('uk-hidden', data.total == 0).data('pagination').render(data.total);
            $('.uk-alert', form).toggleClass('uk-hidden', data.total > 0);

            rowselect.fetchRows();
        });

        lastselected = false;
    }
});