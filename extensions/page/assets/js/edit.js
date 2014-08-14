require(['jquery', 'system!locale', 'uikit!form-select,datepicker,autocomplete,timepicker', 'domReady!'], function($, system, uikit) {

    var form = $('#js-page'), id = $('input[name="id"]', form), cancel = $('.js-cancel', form), spinner = $('.js-spinner', form), dirty = false;

    // form ajax saving
    form.on('submit', function(e) {

        e.preventDefault();
        e.stopImmediatePropagation();

        spinner.removeClass('uk-hidden');

        //publish up date handling
        $('[name="page[publish_up]"]', form).val($('[data-uk-datepicker][for="publish_up"]', form).val()+' '+$('[data-uk-timepicker][for="publish_up"] input', form).val());
        // publish down date handling
        $('[name="page[publish_down]"]', form).val($('[data-uk-datepicker][for="publish_down"]', form).val()+' '+$('[data-uk-timepicker][for="publish_down"] input', form).val());

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

    $(document).on('focus.datepicker.uikit', '[data-uk-datepicker][for="publish_down"]', function(e) {
        console.log(e);
        var ele = $(this);
        if (!ele.data("datepicker")) {
            e.preventDefault();
            var obj = UI.datepicker(ele, UI.Utils.options(ele.attr("data-uk-datepicker")));
            ele.trigger("focus");
        }
    });

    // check form before leaving page
    window.onbeforeunload = (function() {

        form.on('change', ':input', function() {
            dirty = true;
        });

        return function(e) {
            if (dirty) return system.trans('page.unsaved-form');
        };

    })();

    // url handling
    $('input[name="page[title]"]', form).on('blur', function() {

        var url = $('input[name="page[url]"]', form);

        if (url.val() === '') {
            url.val($(this).val().replace(/^([^\/])/, '/$1').toLowerCase());
        }

    });

    // markdown handling
    $('input[name="page[data][markdown]"]', form).on('change', function() {
        $('#page-content', form).trigger($(this).prop('checked') ? 'enableMarkdown' : 'disableMarkdown');
    });

});