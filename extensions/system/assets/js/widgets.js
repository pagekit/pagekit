require(['jquery', 'uikit!sortable', 'domReady!'], function($, uikit) {

    var rows, form = $('#js-widgets')

        // action button
        .on('click', '[data-action]', function(e) {
            e.preventDefault();
            form.attr('action', $(this).data('action')).submit();
        })

        // select all checkbox
        .on('click', '.js-select-all:checkbox', function() {
            $('.js-select', form).prop('checked', $(this).prop('checked'));
            updateOnSelect();
        })

        // save widgets order on sortable change
        .on('sortable-change', 'ul.uk-sortable', function(e, item, action) {

            var list = $(this);

            $.post(form.data('reorder'), { position: list.data('position'), order: list.data('uksortable').serialize(), _csrf: $('[name="_csrf"]').val() }, function(data) {
                if (action == 'added' || action == 'moved') {
                    uikit.notify(data.message, 'success');
                }

                rows = form.find('.js-widget');
            });

            list.find('select[name^="positions"]').val(list.data('position'));
        })

        // change position via selectbox
        .on('change', 'select[name^="positions"]', function() {

            var select  = $(this),
                li      = select.closest('li'),
                current = li.parent(),
                target  = $('ul[data-position="' + select.val() + '"]');

            target.find('.uk-sortable-empty').remove().end().append(li);

            if (!current.children().length) {
                if (current.data('position')) {
                    current.append('<li class="uk-sortable-empty"></li>');
                } else {
                    current.parent().addClass('uk-hidden');
                }
            }

            target.parent().removeClass('uk-hidden');

            $([current, target]).trigger('sortable-change');

            applyFilters();
        })

        .on('change', 'select[name^="filter"]', function() {
            applyFilters();
        });

    var positions = $('.js-position').each(function() {

        var ele = $(this);
        ele.toggleClass('uk-hidden', ele.find('ul.uk-sortable').children('li').length < 1);
    });

    var filters = form.find(':input[id^="filter"]').each(function() {
        $(this).val(sessionStorage['widgets-filter-' + this.id] || '');
    });

    var filter = {
        pos    : $('#filter-position'),
        title  : $('#filter-title'),
        status : $('#filter-status'),
        type   : $('#filter-type')
    };

    applyFilters();

    function applyFilters() {

        var pos = filter.pos.val();

        if (pos) {
            positions.each(function() {
                var ele = $(this);
                ele.toggleClass('uk-hidden', ele.data('position') != pos);
            });
        } else {
            positions.removeClass('uk-hidden').show();
        }

        var pos_visible = positions.filter(':visible'),
            widgets     = pos_visible.find('.js-widget');

        var title  = filter.title.val().toLowerCase(),
            status = filter.status.val(),
            type   = filter.type.val();

        widgets.each(function() {

            var ele = $(this);

            ele.toggle((title ? ele.data('title').toLowerCase().indexOf(title) !== -1 : true) &&
                (status == '' ? true : (status == ele.data('status'))) &&
                (type == '' ? true : (type == ele.data('type'))));
        });

        pos_visible.each(function() {

            var ele = $(this);

            ele.toggleClass('uk-hidden', ele.find('.js-widget:visible').length < 1);
        });

        filters.each(function() {
            sessionStorage['widgets-filter-' + this.id] = $(this).val();
        });
    }

    $('#filter-title').on('keyup', uikit.Utils.debounce(function() {
        applyFilters();
    }, 200));

    // selections

    var showOnSelect = form.find('.js-show-on-select').addClass('uk-hidden'), lastselected;

    rows = form.find('.pk-table-fake');

    form.on('click', '.js-select', function() {
        updateOnSelect();
    })
    // select via row clicking
    .on('click', '.pk-table-fake', function(e){

        var target = $(e.target), row = $(this), select;

        if(!target.is('a, select, input, [data-action]') && !target.closest('[data-action]').length) {

            if (e.shiftKey && window.getSelection) {
                window.getSelection()[window.getSelection().empty ? 'empty':'removeAllRanges']();
            }

            select = row.find('.js-select:first');

            if (select.length) {

                select.prop('checked', !select.prop('checked'));

                // shift select
                if (e.shiftKey && lastselected) {

                    var start = Math.min(row.index(), lastselected.index()), end = Math.max(rows.index(row), rows.index(lastselected));

                    for(i = start; i <= end; i++) {
                        rows.eq(i).find('.js-select:first').prop('checked', true);
                    }
                }

                if (!e.shiftKey && select.prop('checked')) {
                    lastselected = row;
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
        selected.closest('.pk-table-fake').addClass('pk-table-selected');

        if (!selected.length) {
            lastselected = false;
        }
    }

});