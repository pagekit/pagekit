<article class="uk-article">

    <?php if (!$node->get('title_hide')) : ?>
    <h1 class="uk-article-title"><?= $page->getTitle() ?></h1>
    <?php endif ?>

    <?= $page->getContent() ?>

</article>
