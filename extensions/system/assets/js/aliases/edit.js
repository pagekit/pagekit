require(['jquery', 'system!linkpicker'], function($, system) {

    // source
    var $source = $('[name="source"]'), $form = $source.closest('form'), $nosource = $('[data-msg="no-source"]', $form);

    // URL picker
    system.linkpicker($source, { context: 'urlalias' });

    $form.on('submit', function() {
        if (!$source.val()) {
            $nosource.removeClass('uk-hidden');
            return false;
        }
    }).on('change', $source, function() {
        $nosource.addClass('uk-hidden');
    });

});