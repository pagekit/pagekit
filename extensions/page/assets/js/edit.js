require(['jquery', 'domReady!'], function($) {

    var form = $('#js-page');

    $('a.js-access').text($('select.js-access option:selected').text());

    var status   = $('input[name="page[status]"]'),
        statuses = $('.js-status').on('click', function(e){
            e.preventDefault();
            status.val(statuses.addClass('uk-hidden').not(this).removeClass('uk-hidden').data('status'));
        });

    if (status.val() == '') status.val(0);

    statuses.eq(status.val()).removeClass('uk-hidden');

});