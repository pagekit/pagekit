require(['jquery', 'uikit!sortable', 'domReady!'], function($, uikit) {

    var form = $('#js-access-level'),
        doaction = function(element) {
            element.closest('form').attr('action', element.data('action')).submit();
        },
        modal;

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
    });

    // edit button
    form.on('click', '[data-edit]', function(e) {
        e.preventDefault();

        if (!modal) {
            modal = uikit.modal('#modal-access-level');
        }

        modal.show();

        var form = $('form', modal.element);

        form.attr('action', $(this).data('edit'));
        form.find('input:first').val($(this).data('name')).focus();
    });

    var prioUpdateUrl = form.find('.pk-sortable').on('sortable-change',function() {

        var data = {};

        $(this).data('uksortable').list().forEach(function(item) {
            data[item.id] = item.order;
        });

        $.post(prioUpdateUrl, { order: data, _csrf: $('[name="_csrf"]').val() }, function(res) {
            uikit.notify(data.message || 'Access order updated', 'success');
        });

    }).data('updateUrl');

    // auto-save
    $(document).on('click', '#js-access-level-roles input[type="checkbox"]', uikit.Utils.debounce(function() {

        var form = $(this).closest('form#js-access-level-roles');

        $.post(form.attr('action'), form.serialize(), function(data) {
            uikit.notify(data.message || 'Roles saved', 'success');
        });
    }, 1000));

});
