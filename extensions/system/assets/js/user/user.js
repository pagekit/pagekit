require(['jquery', 'uikit!pagination', 'rowselect', 'gravatar', 'domReady!'], function($, uikit, RowSelect, gravatar) {

    var form         = $('#js-user'),
        showOnSelect = form.find('.js-show-on-select').addClass('uk-hidden'),
        table        = $('.js-table-users').on('selected-rows', function(e, rows) { showOnSelect[rows.length ? 'removeClass':'addClass']('uk-hidden'); }),
        doaction     = function(element) {
            element.closest('form').attr('action', element.data('action')).submit();
        },
        rowselect    = new RowSelect(table),
        pagination   = $('[data-uk-pagination]', form),
        page         = $('[name="page"]', form);

    // action button
    form.on('click', '[data-action]', function(e) {
        e.preventDefault();

        $.post($(this).data('action'), form.serialize(), function(data) {
            uikit.notify(data.message, data.error ? 'danger' : 'success');
            selectPage(page.val());
        });
    });

    // submit filters
    form.on('keyup', 'input[name^="filter"]', uikit.Utils.debounce(function() {
        selectPage(0);
    }, 200))
    .on('change', 'select[name^="filter"]', function() {
        selectPage(0);
    })
    .on('submit', function(e) {
        e.preventDefault();
    });

    // pagination
    pagination.on('uk.pagination.select', function(e, index) {
        page.val(index);

        $.post(form.attr('action'), form.serialize(), function(data) {
            table.html(data.table);
            pagination.toggleClass('uk-hidden', data.total < 2).data('pagination').render(data.total);
            $('.uk-alert', form).toggleClass('uk-hidden', data.total > 0);
            rowselect.fetchRows();
            showAvatar();
        });
    });

    // show avatar
    showAvatar();

    function showAvatar(index) {
        $('img[data-avatar]', form).each(function() {
            $(this).attr('src', gravatar.url($(this).data('avatar'), {s: 80, d: 'mm', r: 'g'}));
        });
    }

    function selectPage(index) {
        pagination.data('pagination').selectPage(index);
    }
});