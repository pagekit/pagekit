require(['jquery', 'uikit!sortable,notify', 'domReady!'], function($) {

    var form     = $('#js-menu'), csrf = $('input[name=_csrf]', form).val(),
        doaction = function(element) {
            element.closest('form').attr('action', element.data('action')).submit();
        };

    // action button
    form.on('click', '[data-action]', function(e) {
        e.preventDefault();

        var element = $(this);

        if (element.data("confirm")) {
            $.UIkit.modal.confirm(element.data("confirm"), function() {
                doaction(element);
            });
        } else {
            doaction(element);
        }

    })

    // select all checkbox
    .on('click', '.js-select-all:checkbox', function() {
        $('[name="id[]"]:checkbox', form).prop('checked', $(this).prop('checked'));
    })

    // save menu item order on sortable change
    .on('sortable-change', 'ul.uk-sortable', function() {
        $.post(form.attr('action'), { 'order': $(this).data('uksortable').list(), '_csrf': csrf }, function(data) {
            if(data=="success") {
                $.UIkit.notify("Menu order updated", "success");
            } else {
                $.UIkit.notify("Saving menu order failed", "danger");
            }
        });
    });

    // modal menu

    var modal;

    form.on("click", "a[data-edit]", function(e) {

        e.preventDefault();

        if (!modal) {
            modal = new $.UIkit.modal.Modal('#modal-menu');
        }

        var data = $(this).data(),
            modalform = modal.element.find("form"),
            modaltext = modal.element.find("input[type='text']:first");

        modaltext.val(data.menuName);
        modalform.attr("action", data.edit);
        modal.show();
    });

});