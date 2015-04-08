<?php foreach ($posts as $post) : ?>
<article class="uk-article">

    <h1 class="uk-article-title"><a href="<?= $view->url('@blog/id', ['id' => $post->getId()]) ?>"><?= $post->getTitle() ?></a></h1>

    <p class="uk-article-meta">
        <?= __('Written by %name% on %date%', ['%name%' => $post->getUser()->getName(), '%date%' => '<time datetime="'.$view->date($post->getDate(), 'Y-m-d H:i:s').'">'.$view->date($post->getDate()).'</time>' ]) ?>
    </p>

    <?php if ($post->getExcerpt()) : ?>
    <div><?= $post->getExcerpt() ?></div>
    <?php else : ?>
    <div><?= $post->getContent() ?></div>
    <?php endif ?>

    <ul class="uk-subnav uk-subnav-line">
        <?php if (isset($post->readmore) && $post->readmore || $post->getExcerpt()) : ?>
        <li><a href="<?= $view->url('@blog/id', ['id' => $post->getId()]) ?>"><?= __('Continue Reading') ?></a></li>
        <?php endif ?>
        <?php if ($post->isCommentable() || $post->getCommentCount()) : ?>
        <li><a href="<?= $view->url('@blog/id#comments', ['id' => $post->getId()]) ?>"><?= _c('{0} No comments|{1} %num% Comment|]1,Inf[ %num% Comments', $post->getCommentCount(), ['%num%' => $post->getCommentCount()]) ?></a></li>
        <?php endif ?>
    </ul>

</article>
<?php endforeach ?>

<p>
    <ul class="uk-pagination">
        <?php if ($page > 1) : ?>
        <li class="uk-pagination-previous">
            <a href="<?= $view->url('@blog/page', ['page' => $page-1]) ?>"><?= __('Newer posts') ?></a>
        </li>
        <?php endif ?>
        <?php if ($page < $total) : ?>
        <li class="uk-pagination-next">
            <a href="<?= $view->url('@blog/page', ['page' => $page+1]) ?>"><?= __('Older posts') ?></a>
        </li>
        <?php endif ?>
    </ul>
</p>
