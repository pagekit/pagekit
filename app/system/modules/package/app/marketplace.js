jQuery(function ($) {

    var vm = new Vue({

        el: '#marketplace',

        data: $.extend(window.$marketplace, {
            search: ''
        })

    });

});
