jQuery(function($) {

    var $comments = $('.js-comments'), $respond = $('.js-respond', $comments), $cancel = $('.js-cancel-reply', $respond), $parent = $('[name="comment[parent_id]"]', $respond);

    $comments
        .on('click', '.js-reply', function(e) {
            e.preventDefault();

            var $comment = $(this).closest('[data-comment]');

            $respond.appendTo($comment);
            $parent.val($comment.data('comment'));
            $cancel.show();
        })
        .on('click', '.js-cancel-reply', function(e) {
            e.preventDefault();

            $respond.appendTo($comments);
            $parent.val(0);
            $cancel.hide();
        });

    $cancel.hide();

    if (window['localStorage']) {

        $('[name="comment[author]"]').val(localStorage.getItem('blog.comments.author'));
        $('[name="comment[email]"]').val(localStorage.getItem('blog.comments.email'));
        $('[name="comment[url]"]').val(localStorage.getItem('blog.comments.url'));

        $('form', $respond).on('submit', function() {
            if ($('[name="comment[author]"]').length) {
                localStorage.setItem('blog.comments.author', $('[name="comment[author]"]').val());
                localStorage.setItem('blog.comments.email', $('[name="comment[email]"]').val());
                localStorage.setItem('blog.comments.url', $('[name="comment[url]"]').val());
            }
        });
    }

});