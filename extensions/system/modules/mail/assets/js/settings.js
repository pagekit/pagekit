require(['jquery', 'system', 'uikit', 'domReady!'], function($, system, uikit) {

    // change mailer
    $('[name="option[system:mail.driver]"]').on('change',function() {
        $('[data-smtp]').toggle($(this).val() === 'smtp');
    }).trigger('change');

    // test SMTP
    $('[data-smtp-test]').on('click', function() {

        var data = {};

        $('[name^="option[system:mail"]').each(function() {
            data[$(this).attr('name')] = $(this).val();
        });

        $.post($(this).data('smtp-test'), $.extend(data, system.csrf.params), function(data) {
            if (data) {
                uikit.notify(data.message, data.success ? 'success' : 'danger');
            }
        }, 'json').fail(function() {
            uikit.notify('Ajax request to server failed.', 'danger');
        });
    });

    // test mail
    $('[data-mail-test]').on('click', function() {

        var data = {};

        $('[name^="option[system:mail"]').each(function() {
            data[$(this).attr('name')] = $(this).val();
        });

        $.post($(this).data('mail-test'), $.extend(data, system.csrf.params), function(data) {
            if (data) {
                uikit.notify(data.message, data.success ? 'success' : 'danger');
            }
        }, 'json').fail(function() {
            uikit.notify('Ajax request to server failed.', 'danger');
        });
    });

});
