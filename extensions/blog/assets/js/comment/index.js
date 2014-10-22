require(['jquery', 'uikit!pagination', 'rowselect', 'tmpl!comment.reply', 'domReady!'], function($, uikit, RowSelect, tmpl) {

    var form         = $('#js-comments'),
        showOnSelect = form.find('.js-show-on-select').addClass('uk-hidden'),
        table        = $('.js-table', form).on('selected-rows', function(e, rows) { showOnSelect.toggleClass('uk-hidden', !rows.length); }),
        rowselect    = new RowSelect(table),
        pagination   = $('[data-uk-pagination]', form),
        page         = $('[name="page"]', form),
        post         = $('[name="post"]', form);

    // action button
    form.on('click', '[data-action]', function(e) {
        e.preventDefault();

        $.post($(this).data('action'), form.serialize(), function(data) {
            uikit.notify(data.message, data.error ? 'danger' : 'success');
            selectPage(page.val());
        });
    })

    // submit filters
    .on('keyup', 'input[name^="filter"]', uikit.Utils.debounce(function() {
        selectPage(0);
    }, 200))
    .on('change', 'select[name^="filter"]', function() {
        selectPage(0);
    })
    .on('submit', function(e) {
        e.preventDefault();
    })

    // comment filter
    .on('click', '[data-filter="post"]', function(e) {
        e.preventDefault();

        post.val($(this).data('value'));
        selectPage(0);
    })

    // comment edit
    .on('click', '[data-edit]', function(e) {
        e.preventDefault();
        removeEditor();

        var row = $(this).closest('.js-comment');

        $.post(row.data('url'), function(data) {
            $(data).insertAfter(row.hide());
        });
    })

    // comment reply
    .on('click', '[data-reply]', function(e) {
        e.preventDefault();
        removeEditor();

        var row = $(this).closest('.js-comment');

        $(tmpl.render('comment.reply', row.data())).insertAfter(row);
    })

    // comment editor
    .on('click', '.js-editor [data-save]', function(e) {
        e.preventDefault();

        var editor = $(this).closest('.js-editor'), reply = editor.hasClass('js-reply');

        $.post(editor.data('url'), $('input,select,textarea', editor), function(data) {

            if (!data.error) {
                removeEditor();
                selectPage(reply ? 0 : page.val());
            }

            uikit.notify(data.message, data.error ? 'danger' : 'success');
        });
    })
    .on('click', '.js-editor [data-cancel]', function(e) {
        e.preventDefault();
        removeEditor();
    });

    function removeEditor() {
        $('.js-editor').prev('.js-comment').show().end().remove();
    }

    // pagination
    pagination.on('uk.pagination.select', function(e, index) {
        page.val(index);

        $.post(form.attr('action'), form.serialize(), function(data) {
            table.html(data.table);
            pagination.toggleClass('uk-hidden', data.total < 2).data('pagination').render(data.total);
            $('.uk-alert', form).toggleClass('uk-hidden', data.total > 0);
            rowselect.fetchRows();
        });
    });

    function selectPage(index) {
        pagination.data('pagination').selectPage(index);
    }

});