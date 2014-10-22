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
    var oauthData = $.parseJSON(tmpl.get('oauth.data')),
        list      = $("#oauth-service-list"),
        dropdown  = $("#oauth-service-dropdown");

    $.each(oauthData, function (service, data) {
        var li;

        data.service = service;
        dropdown.append(li = $('<li id="'+data.service+'_link"><a href="#">'+data.service+'</a></li>').click(function() {
            $(this).hide();
            appendService(data);
        }));

        if ('client_id' in data) {
            appendService(data);
            li.hide();
        }
    });

    $("form").submit(function (e) {
        e.preventDefault();
        $.each($(this).find("[name='service-container']"), function() {
            if (!$(this).find(".js-client-id").val() && !$(this).find(".js-client-secret").val()) {
                $(this).remove();
            }
        });
        $(this).unbind('submit').submit();
    });

    function appendService (data)
    {
       var container;

       $("#"+data.service+"-container", list).remove();
       list.append(container = $(tmpl.render('settings.oauth', data)));
       $("[data-info][data-info!='"+data.service+"']", container).remove();
       $(".js-remove", container).click(function (e) {
           e.preventDefault();
           $(this).parent().remove();
           $("#"+data.service+"_link").show();
       });
    }
});