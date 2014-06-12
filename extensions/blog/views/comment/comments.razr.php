@script('blog.post-comments', 'blog/js/comment/post.js', 'jquery')

<div class="js-comments uk-margin">

    <h2>@trans('Comments (%count%)', ['%count%' => post.numComments])</h2>

    <ul class="uk-comment-list">

        @foreach(app.comments.tree(post.comments) as child)
        @include('view://blog/comment/comment.razr.php', ['node' => child, 'comment' => child.comment])
        @endforeach

    </ul>

    @if (post.commentable)
    @include('view://blog/comment/respond.razr.php')
    @endif

</div>