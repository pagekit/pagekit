<article class="uk-article<?= $node->theme['alignment'] ? ' uk-text-center' : '' ?>">

    <?php if (!$node->theme['title_hide']) : ?>
    <h1 class="<?= $node->theme['title_large'] ? 'uk-heading-large' : 'uk-article-title' ?>"><?= $page->title ?></h1>
    <?php endif ?>

    <?= $page->content ?>

</article>
