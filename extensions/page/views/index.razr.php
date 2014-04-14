<article class="uk-article">
    <h1 class="uk-article-title">@page.title</h1>
    @app.content.applyPlugins(page.content, ['markdown' => page.get('markdown'), 'page' => page])
</article>