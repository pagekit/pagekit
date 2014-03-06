require(['jquery', 'uikit!notify,sticky', 'domReady!'], function($) {

    var form = $('#js-permission');

    // handle inherited permissions
    $('input.pk-checkbox', form).each(function() {
        var checkbox = $(this);

        checkbox.after('<div class="pk-checkbox-fake"><div></div><input type="checkbox" checked disabled></div>');

        if (checkbox.is(":checked")) {
            checkbox.closest('td').addClass('pk-permission-enabled');
        }

        checkbox.on("click clicked", function() {
            checkbox.closest('td')[checkbox.is(":checked") ? "addClass":"removeClass"]('pk-permission-enabled');
        });
    });

    $('input[name^="permissions[2]"]:checked', form).closest('tr').addClass('pk-permission-inherited');

    form.on('change', 'input[name^="permissions[2]"]', function() {
        $(this).closest('tr').toggleClass('pk-permission-inherited', $(this).is(':checked'));
    });

    form.on('click', '.pk-checkbox-fake', function() {
        $(this).prev().prop('checked', true).trigger("clicked");
    });

    // auto-save
    form.on("click", "input[type='checkbox']", $.UIkit.Utils.debounce(function() {
        $.post(form.attr("action"), form.serialize(), function(data) {
            $.UIkit.notify(data.message || "Permissions saved");
        });
    }, 1000));

});