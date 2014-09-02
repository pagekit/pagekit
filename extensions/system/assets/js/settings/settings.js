require(['jquery', 'require', 'system!linkpicker', 'uikit!form-password', 'tmpl!oauth.data,settings.oauth', 'domReady!'], function($, req, system, uikit, tmpl) {

    // switcher
    var tabs = $('[data-tabs]').on('uk.switcher.show', function(e, active) {
        $('input[name=tab]').val(active.prevAll().length);
    });

    uikit.tab(tabs, { connect: '#tab-content', active: tabs.data('tabs') });

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

    // URL picker
    system.linkpicker('[name="option[system:app.frontpage]"]', { context: 'frontpage' });

    // OAuth
    var oauthData = $.parseJSON(tmpl.get('oauth.data')), container;

    $.each(oauthData, function (service, data) {
        data.service = service;
        if ('client_id' in data) {
            $("#oauth-service-list").append(container = $(tmpl.render('settings.oauth', data)));
            $("[data-info][data-info!='"+service+"']", container).remove();
        } else {
            $("#oauth-service-dropdown").append($('<li><a href="#">'+service+'</a></li>').click(function() {
                $(this).remove();
                $("#oauth-service-list").append(container = $(tmpl.render('settings.oauth', data)));
                $("[data-info][data-info!='"+service+"']", container).remove();
            }));
        }
    });

    $("form").submit(function(e) {
        e.preventDefault();

        $.each($(this).find("[name='service-container']"), function() {
            if (!$(this).find("#client_id").val() && !$(this).find("#client_secret").val()) {
                $(this).remove();
            }
        });

        $(this).unbind('submit').submit();
    });

});