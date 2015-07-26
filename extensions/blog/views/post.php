<article class="uk-article">

    <h1 class="uk-article-title"><?= $post->getTitle() ?></h1>

    <div class="uk-margin"><?= $post->getContent() ?></div>

    <?= $view->render('blog/comments.php') ?>

</article>
