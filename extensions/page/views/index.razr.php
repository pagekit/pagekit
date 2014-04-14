<article class="uk-article">
    @if(page.get('title', true))
    <h1 class="uk-article-title">@page.title</h1>
    @endif

    @app.content.applyPlugins(page.content, ['markdown' => page.get('markdown'), 'page' => page])
</article>