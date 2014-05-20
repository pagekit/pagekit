require(['jquery', 'uikit!form-select', 'locale', 'domReady!'], function($, uikit, locale) {

    var form = $('#js-page'), id = $('input[name="id"]', form), cancel = $('.js-cancel', form), spinner = $('.js-spinner', form), dirty = false;

    // form ajax saving
    form.on('submit', function(e) {

        e.preventDefault();
        e.stopImmediatePropagation();

        spinner.removeClass('uk-hidden');

        $.post(form.attr('action'), form.serialize(), function(response) {

            dirty = false;
            uikit.notify(response.message, response.error ? 'danger' : 'success');

            if (response.id) {
                id.val(response.id);
                cancel.text(cancel.data('label'));
            }

            spinner.addClass('uk-hidden');
        });
    });

    // check form before leaving page
    window.onbeforeunload = (function() {

        form.on('change', ':input', function() {
            dirty = true;
        });

        return function(e) {
            if (dirty) return locale.trans('page.unsaved-form');
        };

    })();

    // slug handling
    var slug = $('input[name="page[slug]"]', form), title = $('input[name="page[title]"]', form);

    title.on('blur', function() {
        if (!(id.val() - 0)) slug.val('');
        slug.trigger('blur');
    });

    slug.on('blur', function() {
        $.post(slug.data('url'), { slug: slug.val() || title.val(), id: id.val() }, function(data) {
            slug.val(data);
        }, 'json');
    });

    // markdown handling
    $('input[name="page[data][markdown]"]', form).on('change', function() {
        $('#page-content', form).trigger($(this).prop('checked') ? 'enableMarkdown' : 'disableMarkdown');
    });

});