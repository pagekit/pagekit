<?php $view->script('post', 'blog:app/bundle/post.js', 'vue') ?>

<article class="uk-article">

    <?php if ($image = $post->get('image')): ?>
    <img src="<?= $image ?>">
    <?php endif ?>

    <h1 class="uk-article-title"><?= $post->title ?></h1>

    <div class="uk-margin"><?= $post->content ?></div>

    <?= $view->render('blog/comments.php') ?>

</article>
