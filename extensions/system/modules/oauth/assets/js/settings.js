//require(['jquery', 'tmpl!oauth.data,settings.oauth', 'domReady!'], function ($, tmpl) {
//
//    // OAuth
//    var oauthData = $.parseJSON(tmpl.get('oauth.data')),
//        list = $("#oauth-service-list"),
//        dropdown = $("#oauth-service-dropdown");
//
//    $.each(oauthData, function (service, data) {
//        var li;
//
//        data.service = service;
//        dropdown.append(li = $('<li id="' + data.service + '_link"><a href="#">' + data.service + '</a></li>').click(function () {
//            $(this).hide();
//            appendService(data);
//        }));
//
//        if ('client_id' in data) {
//            appendService(data);
//            li.hide();
//        }
//    });
//
//    $("form").submit(function (e) {
//        e.preventDefault();
//        $.each($(this).find("[name='service-container']"), function () {
//            if (!$(this).find(".js-client-id").val() && !$(this).find(".js-client-secret").val()) {
//                $(this).remove();
//            }
//        });
//        $(this).unbind('submit').submit();
//    });
//
//    function appendService(data) {
//        var container;
//
//        $("#" + data.service + "-container", list).remove();
//        list.append(container = $(tmpl.render('settings.oauth', data)));
//        $("[data-info][data-info!='" + data.service + "']", container).remove();
//        $(".js-remove", container).click(function (e) {
//            e.preventDefault();
//            $(this).parent().remove();
//            $("#" + data.service + "_link").show();
//        });
//    }
//});
