require(['jquery', 'tmpl!user.info', 'uikit!form-password', 'gravatar', 'domReady!'], function($, tmpl, uikit, gravatar) {

    var form = $('#js-user'), info = $('.js-info', form), avatar = $('.js-avatar', form);

    // submit form
    form.on('submit', function(e) {
        e.preventDefault();

        $.post($(this).attr('action'), form.serialize(), function(data) {

            if (data.user) {
                $('[name="id"]', form).val(data.user.id);
                info.html(tmpl.render('user.info', data.user));
            }

            uikit.notify(data.message || data.error, data.error ? 'danger' : 'success');
        });
    });

    // show avatar
    $('[name="user[email]"]', form).on('change', function() {
        avatar.html('<img src="' + gravatar.url($(this).val(), {s: 300, d: 'mm', r: 'g'}) + '" class="uk-border-circle" height="150" width="150">');
    }).trigger('change');

});