require(['jquery', 'require', 'urlpicker', 'uikit!notify,form-password', 'domReady!'], function($, req, Picker, UI) {

    // switcher
    var tabs = $('[data-tabs]').on('uk.switcher.show', function(e, active) {
        $('input[name=tab]').val(active.prevAll().length);
    });

    var tabui = new UI.tab(tabs, {connect: '#tab-content', active: tabs.data('tabs') });

    // change mailer
    $('[name$="option[system:mail.driver]"]').on('change', function() {
        $('[data-smtp]').toggle($(this).val() === 'smtp');
    }).trigger('change');

    // test SMTP
    $('[data-smtp-test]').on('click', function() {
        $.post($(this).data('smtp-test'), $('[data-smtp] :input').serialize(), function(data) {
            if (data) {
                $.UIkit.notify(data.message, data.success ? 'success': 'danger');
            }
        }, 'json').fail(function() {
            $.UIkit.notify('Ajax request to server failed.', 'danger');
        });
    });

    // test mail
    $('[data-mail-test]').on('click', function() {
        $.post($(this).data('mail-test'), $('[data-email] :input').serialize(), function(data) {
            if (data) {
                $.UIkit.notify(data.message, data.success ? 'success': 'danger');
            }
        }, 'json').fail(function() {
            $.UIkit.notify('Ajax request to server failed.', 'danger');
        });
    });

    // URL picker
    new Picker('[name="option[system:app.frontpage]"]');
});