require(['jquery', 'uikit!sortable,notify', 'domReady!'], function($) {

    var form     = $('#js-role'),
        formPerm = $('#js-role-permissions'),
        doaction = function(element) {
            element.closest('form').attr('action', element.data('action')).submit();
        },
        modal;

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
    });

    // edit button
    form.on('click', '[data-edit]', function(e) {
        e.preventDefault();

        if (!modal) {
            modal = new $.UIkit.modal.Modal('#modal-role');
        }

        modal.show();

        var form = $('form', modal.element);

        form.attr('action', $(this).data('edit'));
        form.find('input:first').val($(this).data('name')).focus();
    });

    // handle inherited permissions
    formPerm.find('.pk-checkbox').each(function() {

        var checkbox = $(this);

        checkbox.after('<div class="pk-checkbox-fake"><div></div><input type="checkbox" checked disabled></div>');

        if (checkbox.is(":checked")) {
            checkbox.closest('td').addClass('pk-permission-enabled');
        }

        checkbox.on("click clicked", function() {
            checkbox.closest('td')[checkbox.is(":checked") ? "addClass":"removeClass"]('pk-permission-enabled');
        });
    });

    formPerm.on('click', '.pk-role-inherited input, .pk-role-inherited .pk-checkbox-fake', function() {

        var $this = $(this);

        $this.closest('td').toggleClass('pk-role-enabled');

        if ($this.is('.pk-checkbox-fake')) {
            $this.prev().prop('checked', true);
        }
    });

    var prioUpdateUrl = form.find('.pk-sortable').on('sortable-change', function() {

        var data = {};

        $(this).data('uksortable').list().forEach(function(item) {
            data[item.id] = item.order;
        });

        $.post(prioUpdateUrl, {'order': data}, function(res) {
            $.UIkit.notify(data.message || "Roles order updated");
        });

    }).data('updateUrl');

    // auto-save
    $(document).on("click", "#js-role-permissions input[type='checkbox']", $.UIkit.Utils.debounce(function() {

        var form = $(this).closest("form#js-role-permissions");

        $.post(form.attr("action"), form.serialize(), function(data) {
            $.UIkit.notify(data.message || "Permissions saved");
        });
    }, 1000));

});