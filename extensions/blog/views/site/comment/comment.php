<?php $maxlevel = $blog->config('comments.max_depth') <= $node->getDepth() ?>
<li id="comment-<?= $comment->getId() ?>">

    <article class="uk-comment" data-comment="<?= $comment->getId() ?>">

        <header class="uk-comment-header">

            <?= $view->gravatar($comment->getEmail(), ['size' => 100, 'attrs' => ['width' => '50', 'height' => '50', 'alt' => $comment->getAuthor(), 'class' => 'uk-comment-avatar']]) ?>

            <h3 class="uk-comment-title"><?= $comment->getAuthor() ?></h3>

            <ul class="uk-comment-meta uk-subnav uk-subnav-line">
                <li>
                    <time datetime="<?= $intl->date($comment->getCreated(), 'Y-m-d H:i:s') ?>"><?= __('%date% at %time%', ['%date%' => $intl->date($comment->getCreated()), '%time%' => $intl->date($comment->getCreated(), 'H:i:s')]) ?></time>
                </li>
                <li>
                    <a href="<?= $view->url('@blog/id', ['id' => $comment->getPostId()]) ?>#comment-<?= $comment->getId() ?>">#</a>
                </li>
            </ul>

        </header>

        <div class="uk-comment-body">

            <p><?= $comment->getContent() ?></p>

            <?php if ($post->getCommentStatus() && !$maxlevel) : ?>
            <p><a class="js-reply" href="#"><i class="uk-icon-reply"></i> <?= __('Reply') ?></a></p>
            <?php endif ?>

        </div>

    </article>

    <?php if ($node->hasChildren() && !$maxlevel) : ?>
    <ul>
        <?php foreach ($node->getChildren() as $child) : ?>
        <?= $view->render('blog:views/site/comment/comment.php', ['node' => $child, 'comment' => $child->getComment(), 'post' => $post, 'blog' => $blog]) ?>
        <?php endforeach ?>
    </ul>
    <?php endif ?>

</li>

<?php if ($node->hasChildren() && $maxlevel) : ?>
    <?php foreach ($node->getChildren() as $child) : ?>
    <?= $view->render('blog:views/site/comment/comment.php', ['node' => $child, 'comment' => $child->getComment(), 'post' => $post, 'blog' => $blog]) ?>
    <?php endforeach ?>
<?php endif ?>
