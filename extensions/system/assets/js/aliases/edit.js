require(['urlpicker'], function(Picker) {
    // URL picker
    new Picker('[name="source"]', { typeFilter: ['/'] });
});