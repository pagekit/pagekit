<?php $view->script('posts', 'blog:app/bundle/posts.js', 'vue') ?>

<?php foreach ($posts as $post) : ?>
<article class="uk-article" v-cloak>

    <?php if ($image = $post->get('image')): ?>
    <img src="<?=$image?>">
    <?php endif ?>

    <h1 class="uk-article-title"><a href="<?= $view->url('@blog/id', ['id' => $post->id]) ?>"><?= $post->title ?></a></h1>

    <p class="uk-article-meta">
        <?= __('Written by %name% on %date%', ['%name%' => $post->user->name, '%date%' => '<time datetime="'.$post->date->format(\DateTime::ISO8601).'">{{ "'.$post->date->format(\DateTime::ISO8601).'" | date "longDate" }}</time>' ]) ?>
    </p>

    <div class="uk-margin"><?= $post->excerpt ?: $post->content ?></div>

    <ul class="uk-subnav">

        <?php if (isset($post->readmore) && $post->readmore || $post->excerpt) : ?>
        <li><a href="<?= $view->url('@blog/id', ['id' => $post->id]) ?>"><?= __('Continue Reading') ?></a></li>
        <?php endif ?>

        <?php if ($post->isCommentable() || $post->comment_count) : ?>
        <li><a href="<?= $view->url('@blog/id#comments', ['id' => $post->id]) ?>"><?= _c('{0} No comments|{1} %num% Comment|]1,Inf[ %num% Comments', $post->comment_count, ['%num%' => $post->comment_count]) ?></a></li>
        <?php endif ?>

    </ul>

</article>
<?php endforeach ?>


<?php if ($total > 1) : ?>
<ul class="uk-pagination">

    <?php if ($page > 1) : ?>
    <li class="uk-pagination-previous">
        <a href="<?= $view->url('@blog/page', ['page' => $page - 1]) ?>"><?= __('Newer posts') ?></a>
    </li>
    <?php endif ?>

    <?php if ($page < $total) : ?>
    <li class="uk-pagination-next">
        <a href="<?= $view->url('@blog/page', ['page' => $page + 1]) ?>"><?= __('Older posts') ?></a>
    </li>
    <?php endif ?>

</ul>
<?php endif ?>
