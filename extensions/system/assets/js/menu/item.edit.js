require(['linkpicker'], function(Picker) {

    // source
    var $link = $('[name="item[url]"]'), $form = $link.closest('form'), $nolink = $('[data-msg="no-link"]', $form);

    // URL picker
    new Picker($link);

    $form.on('submit', function() {
        if (!$link.val()) {
            $nolink.removeClass('uk-hidden');
            return false;
        }
    }).on('change', $link, function() {
        $nolink.addClass('uk-hidden');
    });

});