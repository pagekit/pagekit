require(['linkpicker', 'uikit'], function(Picker, uikit) {

    // source
    var $source = $('[name="source"]'), $form = $source.closest('form'), $nosource = $('[data-msg="no-source"]', $form);

    // URL picker
    new Picker($source, { context: 'urlalias' });

    $form.on('submit', function() {
        if (!$source.val()) {
            $nosource.removeClass('uk-hidden');
            return false;
        }
    }).on('change', $source, function() {
        $nosource.addClass('uk-hidden');
    });

});