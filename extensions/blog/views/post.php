<article class="uk-article">

    <h1 class="uk-article-title"><?= $post->getTitle() ?></h1>

    <?php if ($image = $post->get('image')): ?>
    <img src="<?=$image?>" />
    <?php endif ?>

    <div class="uk-margin"><?= $post->getContent() ?></div>

    <?= $view->render('blog/comments.php') ?>

</article>
