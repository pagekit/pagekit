<?php $view->script('post', 'blog:app/bundle/post.js', ['vue', 'comments']) ?>

<article class="uk-article" v-cloak>

    <?php if ($image = $post->get('image')): ?>
    <img src="<?= $image ?>">
    <?php endif ?>

    <h1 class="uk-article-title"><?= $post->title ?></h1>

    <?php if (!$blog->config('posts.excerpt_hide') && $post->excerpt): ?>
    <div class="uk-margin"><?= $post->excerpt ?></div>
    <?php endif ?>

    <div class="uk-margin"><?= $post->content ?></div>

    <?= $view->render('blog/comments.php') ?>

</article>
