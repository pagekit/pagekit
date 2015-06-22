<?php
$view->script('comments', 'blog:app/bundle/comments.js', ['vue', 'uikit-notify'])
?>

<article class="uk-article">

    <h1 class="uk-article-title"><?= $post->getTitle() ?></h1>

    <div><?= $post->getContent() ?></div>

    <div id="comments"></div>

</article>
