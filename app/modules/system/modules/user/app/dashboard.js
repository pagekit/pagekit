jQuery(function($) {

    $('[data-user]').each(function() {
        new Vue({el: this});
    });

});