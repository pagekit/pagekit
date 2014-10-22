require(['jquery', 'system', 'uikit!nestable,form-select', 'rowselect', 'domReady!'], function($, system, uikit, RowSelect) {

    var form = $('#js-widgets')

        // action button
        .on('click', '[data-action]', function(e) {
            e.preventDefault();
            form.attr('action', $(this).data('action')).submit();
        })

        // save widgets order on nestable change
        .on('uk.nestable.change', 'ul.uk-nestable', function(e, item, action) {

            var list = $(this);

            $.post(form.data('reorder'), $.extend({position: list.data('position'), order: list.data('nestable').serialize()}, system.csrf.params), function(data) {

                if (action == 'added' || action == 'moved') {
                    uikit.notify(data.message, 'success');
                }

                rowselect.fetchRows();
            });

            list.find('select[name^="positions"]').val(list.data('position'));
        })

        // change position via selectbox
        .on('change', 'select[name^="positions"]', function() {

            var select  = $(this),
                li      = select.closest('li.js-widget'),
                current = li.parent(),
                target  = $('ul.uk-nestable[data-position="' + select.val() + '"]');

            target.find('.uk-nestable-empty').remove().end().append(li);

            if (!current.children().length) {
                if (current.data('position')) {
                    current.append('<li class="uk-nestable-empty"></li>');
                } else {
                    current.parent().addClass('uk-hidden');
                }
            }

            target.parent().removeClass('uk-hidden');

            current.trigger('uk.nestable.change');
            target.trigger('uk.nestable.change', [null, 'moved']);

            applyFilters();
        })

        .on('change', 'select[name^="filter"]', function() {
            applyFilters();
        });

    // check for empty positions
    try{

        var lists    =  form.find('ul.uk-nestable'),
            observer = new uikit.support.mutationobserver(function(mutations) {
                lists.each(function(){
                    var list = $(this);
                    if (!list.children().length) {
                        list.append('<li class="uk-nestable-empty"></li>');
                    }
                });
            });

        // pass in the target node, as well as the observer options
        observer.observe(form[0], { childList: true, subtree: true });

    } catch(e) {}


    var positions = $('.js-position').each(function() {

        var ele = $(this);
        ele.toggleClass('uk-hidden', ele.find('ul.uk-nestable').children('li').length < 1);
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

    filter.title.on('keyup', uikit.Utils.debounce(function() {
        applyFilters();
    }, 200));


    // selections

    var showOnSelect = form.find('.js-show-on-select').addClass('uk-hidden'),
        table        = form.on('selected-rows', function(e, rows) { showOnSelect[rows.length ? 'removeClass':'addClass']('uk-hidden'); }),
        rowselect    = new RowSelect(table, { 'rows': '.pk-table-fake' });

});