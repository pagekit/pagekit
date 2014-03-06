require(['jquery', 'uikit!sortable,notify', 'domReady!'], function($) {

    var form = $('#js-dashboard'), params = form.data();

    // action button
    form.on('click', '[data-action]', function(e) {
        e.preventDefault();
        form.attr('action', $(this).data('action')).submit();
    })

    // select all checkbox
    .on('click', '.js-select-all', function() {
        form.find('.js-select').prop('checked', $(this).prop('checked'));
    }).on('click', '.js-select', function(){
        form.find('.js-select-all').prop('checked', false);
    })

    // save widgets order on sortable change
    .on('sortable-change', 'ul.uk-sortable', function(e) {
        $.post(params.reorder, {order: $(this).data('uksortable').serialize()}, function(response){
            $.UIkit.notify(response.message || "Widgets order updated");
        });
    });

});