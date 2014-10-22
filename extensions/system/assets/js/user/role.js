require(['jquery', 'system', 'uikit!nestable', 'domReady!'], function($, system, uikit) {

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
            modal = uikit.modal('#modal-role');
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

        if (checkbox.is(':checked')) {
            checkbox.closest('td').addClass('pk-permission-enabled');
        }

        checkbox.on('click clicked', function() {
            checkbox.closest('td').toggleClass('pk-permission-enabled', checkbox.is(':checked'));
        });
    });

    formPerm.on('click', '.pk-role-inherited input, .pk-role-inherited .pk-checkbox-fake', function() {

        var $this = $(this);

        $this.closest('td').toggleClass('pk-role-enabled');

        if ($this.is('.pk-checkbox-fake')) {
            $this.prev().prop('checked', true);
        }
    });

    var prioUpdateUrl = form.find('.pk-nestable').on('uk.nestable.change',function() {

        var data = {};

        $(this).data('nestable').list().forEach(function(item) {
            data[item.id] = item.order;
        });

        $.post(prioUpdateUrl, $.extend({order: data}, system.csrf.params), function() {
            uikit.notify(data.message || 'Roles order updated', 'success');
        });

    }).data('updateUrl');

    // auto-save
    $(document).on('click', '#js-role-permissions input[type="checkbox"]', uikit.Utils.debounce(function() {

        var form = $(this).closest('form#js-role-permissions');

        $.post(form.attr('action'), form.serialize(), function(data) {
            uikit.notify(data.message || 'Permissions saved', 'success');
        });

    }, 1000));

});