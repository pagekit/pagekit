<article class="uk-article">

    <?php if (!$node->theme['title_hide']) : ?>
    <h1 class="uk-article-title"><?= $page->title ?></h1>
    <?php endif ?>

    <?= $page->content ?>

</article>
