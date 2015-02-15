require(['jquery', 'gravatar', 'domReady!'], function($, gravatar) {

    $('img[data-avatar]').each(function() {
        $(this).attr('src', gravatar.url($(this).data('avatar'), {s: 200, d: 'mm', r: 'g'}));
    });

});