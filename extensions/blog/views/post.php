<article class="uk-article">

    <?php if ($image = $post->get('image')): ?>
    <img src="<?= $image ?>" />
    <?php endif ?>

    <h1 class="uk-article-title"><?= $post->getTitle() ?></h1>

    <?php if ($post->getExcerpt()): ?>
    <div class="uk-margin"><?= $post->getExcerpt() ?></div>
    <?php endif ?>

    <div class="uk-margin"><?= $post->getContent() ?></div>

    <?= $view->render('blog/comments.php') ?>

</article>
