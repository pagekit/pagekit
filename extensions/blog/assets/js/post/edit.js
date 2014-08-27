require(['jquery', 'system!locale', 'uikit!form-select,datepicker,autocomplete,timepicker', 'domReady!'], function($, system, uikit) {

    var form = $('#js-post'), id = $('input[name="id"]', form), cancel = $('.js-cancel', form), spinner = $('.js-spinner', form), dirty = false;

    // form ajax saving
    form.on('submit', function(e) {

        e.preventDefault();
        e.stopImmediatePropagation();

        spinner.removeClass('uk-hidden');

        // date handling
        $('[name="post[date]"]', form).val($('[data-uk-datepicker]', form).val()+' '+$('[data-uk-timepicker] input', form).val());

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
            if (dirty) return system.trans('post.unsaved-form');
        };

    })();

    // slug handling
    $('input[name="post[title]"]', form).on('blur', function() {

        var slug = $('input[name="post[slug]"]', form);

        if (slug.val() === '') {
            slug.val($(this).val().replace(/\s/g, '-').toLowerCase());
        }

    });

    // markdown handling
    $('input[name="post[data][markdown]"]', form).on('change', function() {
        $('#post-content', form).trigger($(this).prop('checked') ? 'enableMarkdown' : 'disableMarkdown');
    });

});