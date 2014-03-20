require(['linkpicker'], function(Picker) {
    // URL picker
    new Picker('[name="source"]', { typeFilter: ['@frontpage'] });
});