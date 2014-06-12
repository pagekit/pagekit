<h2 class="title">@trans('Blog')</h2>

<div>

    @foreach (posts as post)
        <article class="uk-article">

            <header>

                <a href="@url.route('@blog/id', ['id' => post.id])"><h1 class="title">@post.title</h1></a>

                <p class="meta">
                    @trans('Written by %name% on %date%', ['%name%' => post.user.name, '%date%' => '<time datetime="'~post.date|date('Y-m-d H:i:s')~'">'~post.date|date~'</time>' ])
                </p>

            </header>

            <div>
                @post.content
            </div>

            <p>
                <a href="@url.route('@blog/id', ['id' => post.id])">@trans('Continue Reading »')</a>
            </p>
        </article>
    @endforeach

</div>