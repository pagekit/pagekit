jQuery(function($) {

    var comments = $('.js-comments'), respond = $('.js-respond', comments), cancel = $('.js-cancel-reply', respond), parent = $('[name="comment[parent_id]"]', respond), storage = window.localStorage || {};

    comments
        .on('click', '.js-reply', function(e) {
            e.preventDefault();

            var comment = $(this).closest('[data-comment]');

            respond.appendTo(comment);
            parent.val(comment.data('comment'));
            cancel.show();
        })
        .on('click', '.js-cancel-reply', function(e) {
            e.preventDefault();

            respond.appendTo(comments);
            parent.val(0);
            cancel.hide();
        });

    cancel.hide();

    $('[name="comment[author]"]').val(storage['blog.comments.author']);
    $('[name="comment[email]"]').val(storage['blog.comments.email']);
    $('[name="comment[url]"]').val(storage['blog.comments.url']);

    $('form', respond).on('submit', function() {
        if ($('[name="comment[author]"]').length) {
            storage['blog.comments.author'] = $('[name="comment[author]"]').val();
            storage['blog.comments.email'] = $('[name="comment[email]"]').val();
            storage['blog.comments.url'] = $('[name="comment[url]"]').val();
        }
    });

});