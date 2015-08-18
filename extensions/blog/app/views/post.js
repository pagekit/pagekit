jQuery(function ($) {
    $('article').each(function() {
        new Vue({}).$mount(this);
    });
});
