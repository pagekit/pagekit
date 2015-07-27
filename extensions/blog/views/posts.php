<?php foreach ($posts as $post) : ?>
<article class="uk-article">

    <h1 class="uk-article-title"><a href="<?= $view->url('@blog/id', ['id' => $post->getId()]) ?>"><?= $post->getTitle() ?></a></h1>

    <?php if ($image = $post->get('image')): ?>
    <img src="<?=$image?>" />
    <?php endif ?>

    <p class="uk-article-meta">
        <?= __('Written by %name% on %date%', ['%name%' => $post->getUser()->getName(), '%date%' => '<time datetime="'.$intl->date($post->getDate(), 'iso').'">'.$intl->date($post->getDate()).'</time>' ]) ?>
    </p>

    <div class="uk-margin"><?= ($post->getExcerpt()) ? $post->getExcerpt() : $post->getContent() ?></div>

    <ul class="uk-subnav">

        <?php if (isset($post->readmore) && $post->readmore || $post->getExcerpt()) : ?>
        <li><a href="<?= $view->url('@blog/id', ['id' => $post->getId()]) ?>"><?= __('Continue Reading') ?></a></li>
        <?php endif ?>

        <?php if ($post->isCommentable() || $post->getCommentCount()) : ?>
        <li><a href="<?= $view->url('@blog/id#comments', ['id' => $post->getId()]) ?>"><?= _c('{0} No comments|{1} %num% Comment|]1,Inf[ %num% Comments', $post->getCommentCount(), ['%num%' => $post->getCommentCount()]) ?></a></li>
        <?php endif ?>

    </ul>

</article>
<?php endforeach ?>


<?php if ($total > 1) : ?>
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
<?php endif ?>
