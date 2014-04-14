require(['jquery', 'uikit!sortable', 'domReady!'], function($, uikit) {

    var form  = $('#js-menu'), csrf = $('input[name=_csrf]', form).val(),
        table = $('.js-menu-items'),
        doaction = function(element) {
            element.closest('form').attr('action', element.data('action')).submit();
        },
        showOnSelect, lastselected, rows = table.find('li');

    // selections
    showOnSelect = form.find('.js-show-on-select').addClass('uk-hidden');

    form.on('click', '.js-select', function() {
        updateOnSelect();
    })
    // select via row clicking
    .on('click', 'li', function(e){

        var target = $(e.target), li = $(this), select;

        if(!target.is('a, input, [data-action]') && !target.closest('[data-action]').length) {

            if (e.shiftKey && window.getSelection) {
                window.getSelection()[window.getSelection().empty ? 'empty':'removeAllRanges']();
            }

            select = li.find('.js-select:first');

            if (select.length) {

                select.prop('checked', !select.prop('checked'));

                // shift select
                if (e.shiftKey && lastselected) {

                    var start = Math.min(li.index(), lastselected.index()), end = Math.max(li.index(), lastselected.index());

                    for(i = start; i <= end; i++) {
                        rows.eq(i).find('.js-select:first').prop('checked', true);
                    }
                }

                if (!e.shiftKey && select.prop('checked')) {
                    lastselected = li;
                } else {
                    lastselected = false;
                }

                updateOnSelect();
            }
        }
    });

    function updateOnSelect() {
        var selected = form.find('.js-select:checked');
        showOnSelect[selected.length ? 'removeClass':'addClass']('uk-hidden');

        rows.removeClass('pk-table-selected');
        selected.closest('li').addClass('pk-table-selected');

        if (!selected.length) {
            lastselected = false;
        }
    }

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

        // select all checkbox
        .on('click', '.js-select-all:checkbox', function() {
            $('[name="id[]"]:checkbox', form).prop('checked', $(this).prop('checked'));
            updateOnSelect();
        })

        // save menu item order on sortable change
        .on('sortable-change', 'ul.uk-sortable', function() {
            $.post(form.attr('action'), { order: $(this).data('uksortable').list(), _csrf: csrf }, function(data) {
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
            modal = new uikit.modal.Modal('#modal-menu');
        }

        var data      = $(this).data(),
            modalform = modal.element.find('form'),
            modaltext = modal.element.find('input[type="text"]:first');

        modaltext.val(data.menuName);
        modalform.attr('action', data.edit);
        modal.show();
    });

});