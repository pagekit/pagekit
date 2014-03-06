require(['jquery', 'link'], function($, Link) {

    var link = new Link();

    $('form').on('submit', function() {
        link.clearEditForm();
    });

});