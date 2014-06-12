<li id="comment-@comment.id">
    <article data-comment="@comment.id" class="uk-comment ">

        <header class="uk-comment-header">

            @gravatar(comment.email, ['size' => 50, 'attrs' => ['width' => '50', 'height' => '50', 'alt' => comment.author, 'class' => 'uk-comment-avatar']])
            <h3 class="uk-comment-title">@comment.author</h3>

            <ul class="uk-comment-meta uk-subnav uk-subnav-line">
                <li>
                    <time datetime="@comment.created|date('Y-m-d H:i:s')">@trans('%date% at %time%', ['%date%' => comment.created|date, '%time%' => comment.created|date('H:i:s')])</time>
                </li>
                <li>
                    <a href="@url.route('@blog/id', ['id' => comment.threadId])#comment-@comment.id">#</a>
                </li>
            </ul>

        </header>

        <div class="uk-comment-body">

            <p>@comment.content</p>

            <p><a class="js-reply" href="#"><i class="uk-icon-reply"></i> @trans('Reply')</a></p>

        </div>

    </article>
    @if (node.hasChildren())
    <ul>
        @foreach(node.children as child)
        @include('view://blog/comment/comment.razr.php', ['node' => child, 'comment' => child.comment])
        @endforeach
    </ul>
    @endif
</li>