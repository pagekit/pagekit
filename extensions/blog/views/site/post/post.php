<article class="uk-article">

    <h1 class="uk-article-title"><?= $post->getTitle() ?></h1>

    <div><?= $post->getContent() ?></div>

    <?php if ($post->isCommentable() || $post->getCommentCount()) : ?>
    <?= $view->render('blog:views/site/comment/comments.php', ['post' => $post, 'blog' => $blog]) ?>
    <?php endif ?>

</article>
