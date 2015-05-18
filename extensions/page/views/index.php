<article class="uk-article">

    <?php if ($page->get('title', true)) : ?>
    <h1 class="uk-article-title"><?= $page->getTitle() ?></h1>
    <?php endif ?>

    <?= $page->getContent() ?>
</article>
