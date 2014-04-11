require(['jquery','uikit!pagination' , 'domReady!'], function($, uikit) {

    var form = $('#js-pages'), table = $('.js-table', form), pagination = $('[data-uk-pagination]', form), search, prev, showOnSelect;

    // action button
    form.on('click', '[data-action]', function(e) {
        e.preventDefault();

        $.post($(this).data('action'), form.serialize(), function(response) {
            uikit.notify(response.message, response.error ? 'danger' : 'success');
            updateTable();
            updateOnSelect();
        });
    })

    // select all checkbox
    .on('click', '.js-select-all:checkbox', function() {
        $('[name="ids[]"]:checkbox', form).prop('checked', $(this).prop('checked'));
        updateOnSelect();
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

    showOnSelect = form.find('.js-show-on-select').addClass('uk-hidden');

    form.on('click', '.js-select', function() {
        updateOnSelect();
    })

    // select via row clicking
    .on('click', 'tr', function(e){

        var target = $(e.target), tr = $(this), select;

        if(!target.is('input, [data-action]') && !target.closest('[data-action]').length) {
            select = tr.find('.js-select:first');

            if (select.length) {

                if (!e.metaKey) {
                    form.find('.js-select:checked').prop('checked', false);
                }

                select.prop('checked', !select.prop('checked'));
                updateOnSelect();
            }
        }
    });

    function updateTable() {
        $.post(form.data('action'), form.serialize(), function(response) {
            table.html(response.table);
            pagination.toggleClass('uk-hidden', response.table === '').data('pagination').render(response.total);
            $('.uk-alert', form).toggleClass('uk-hidden', response.table !== '');
        });
    }

    function updateOnSelect() {
        var selected = form.find('.js-select:checked');
        showOnSelect[selected.length ? 'removeClass':'addClass']('uk-hidden');

        form.find('tr').removeClass('pk-selected');
        selected.closest('tr').addClass('pk-selected');
    }
});