<?php if ($app['user']->hasAccess('blog: view comments')) : ?>

<div id="comments" class="js-comments uk-margin">

    <?php if ($post->getCommentCount()) : ?>
    <h2 class="uk-h3"><?= __('Comments (%count%)', ['%count%' => $post->getCommentCount()]) ?></h2>
    <ul class="uk-comment-list">

        <?php foreach ($post->getCommentsTree(['order' => $blog->config('comments.order')]) as $child) : ?>
        <?= $view->render('blog:views/site/comment/comment.php', ['node' => $child, 'comment' => $child->getComment(), 'post' => $post, 'blog' => $blog]) ?>
        <?php endforeach ?>

    </ul>
    <?php endif ?>

    <?php if ($post->isCommentable()) : ?>
    <?= $view->render('blog:views/site/comment/respond.php', ['post' => $post, 'blog' => $blog]) ?>
    <?php else : ?>
    <?= __('Comments are closed.') ?>
    <?php endif ?>

</div>

<?php elseif ($app['user']->isAuthenticated()) : ?>

    <p><?= __('You are not allowed to view comments.') ?></p>

<?php else : ?>

    <p><?= __('Please login to view comments.') ?></p>

<?php endif ?>
