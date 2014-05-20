require(['jquery', 'uikit!nestable', 'rowselect', 'domReady!'], function($, uikit, RowSelect) {

    var form         = $('#js-menu'), csrf = $('input[name=_csrf]', form).val(),
        showOnSelect = form.find('.js-show-on-select').addClass('uk-hidden'),
        table        = $('.js-menu-items').parent().on('selected-rows', function(e, rows) { showOnSelect[rows.length ? 'removeClass':'addClass']('uk-hidden'); }),
        rowselect    = new RowSelect(table, { 'rows': '.pk-table-fake' }),
        doaction     = function(element) {
            element.closest('form').attr('action', element.data('action')).submit();
        };


    // action button
    form.on('click', '[data-action]', function(e) {
        e.preventDefault();

        var element = $(this);

        if (element.data('confirm')) {
            uikit.modal.confirm(element.data('confirm'), function() {
                doaction(element);
            });
        } else {
            doaction(element);
        }

    })

        // save menu item order on nestable change
        .on('nestable-change', 'ul.uk-nestable', function() {
            $.post(form.attr('action'), { order: $(this).data('nestable').list(), _csrf: csrf }, function(data) {
                uikit.notify(data.message, 'success');
            }).fail(function() {
                uikit.notify('Saving menu order failed', 'danger');
            });
        });

    // modal menu

    var modal;

    form.on('click', 'a[data-edit]', function(e) {

        e.preventDefault();

        if (!modal) {
            modal = uikit.modal('#modal-menu');
        }

        var data      = $(this).data(),
            modalform = modal.element.find('form'),
            modaltext = modal.element.find('input[type="text"]:first');

        modaltext.val(data.menuName);
        modalform.attr('action', data.edit);
        modal.show();
    });

});