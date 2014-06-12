require(['jquery', 'uikit!pagination', 'rowselect', 'tmpl!comment.edit,comment.reply', 'domReady!'], function($, uikit, RowSelect, tmpl) {

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

    // post filter
    .on('click', '[data-filter="post"]', function(e) {
        e.preventDefault();
        post.val($(this).data('value'));
        selectPage(0);
    })

    // quick actions
    .on('click', '[data-quick-action]', function(e) {
        e.preventDefault();
        removeEditor();

        var row = $(this).closest('.js-comment'), reply = $(this).data('quick-action') == 'reply';
        $(tmpl.render(reply ? 'comment.reply' : 'comment.edit', row.data())).insertAfter(row.hide()).on('submit', 'form', function(e) {
            e.preventDefault();

            $.post($(this).prop('action'), $(this).serialize(), function(data) {
                uikit.notify(data.message, data.error ? 'danger' : 'success');
                selectPage(reply ? 0 : page.val());
            });
        });

    })
    .on('click', '.js-editor .cancel', function(e) {
        e.preventDefault();
        removeEditor();
    });

    function removeEditor() {
        $('.js-editor').prev('.js-comment').show().end().remove();
    }

    // pagination
    pagination.on('uk-select-page', function(e, index) {
        page.val(index);

        $.post(form.data('action'), form.serialize(), function(data) {
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