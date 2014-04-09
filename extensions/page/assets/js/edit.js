require(['jquery', 'uikit', 'domReady!'], function($, uikit) {

    var form = $('#js-page');

    $('a.js-access').text($('select.js-access option:selected').text());
    $('a.js-markdown').text($('select.js-markdown option:selected').text());

    var status   = $('input[name="page[status]"]'),
        statuses = $('.js-status').on('click', function(e){
            e.preventDefault();
            status.val(statuses.addClass('uk-hidden').not(this).removeClass('uk-hidden').data('status'));
        });

    if (status.val() == '') status.val(0);

    statuses.eq(status.val()).removeClass('uk-hidden');

    form.on("submit", function(e){

        e.preventDefault();
        e.stopImmediatePropagation();

        $.post(form.attr("action"), form.serialize(), function(response){

            uikit.notify(response.message, response.error ? 'danger':'success');

            if (response.id) {
                form.find('input[name="id"]').val(response.id);
            }
        });
    });


    var textarea       = $('#page-content'),
        markdownselect = $('select.js-markdown').on('change', function(){
            textarea.trigger(markdownselect.val()=='1' ? 'enableMarkdown':'disableMarkdown');
        });

});