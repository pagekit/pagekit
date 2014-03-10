require(['urlpicker'], function(Picker) {

    // URL picker
    var source = $('[name="source"]'), picker = new Picker(source);

    source.on('resolved', function(e, resolved) {
        $('.js-resolved-url').text(resolved).show(resolved > 0);
    }).on('change', function() {
        picker.resolve();
    }).trigger('change');
});