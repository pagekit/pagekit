<?php if ($root->getDepth() === 0) : ?>
<ul class="uk-nav">
    <?php endif ?>

    <?php foreach ($root->getChildren() as $node) : ?>

        <li class="<?= $node->get('parent') ? 'uk-parent' : ''?><?= $node->get('active') ? ' uk-active' : ''?>">
            <a href="<?= $view->url($node->frontpage ? '/' : $node->getPath()) ?>"><?= $node->getTitle() ?></a>
            <?php if ($node->hasChildren()) : ?>
                <ul class="uk-nav-sub">
                    <?= $view->render('menu', ['root' => $node, 'widget' => $widget]) ?>
                </ul>
            <?php endif ?>
        </li>

    <?php endforeach ?>

    <?php if ($root->getDepth() === 0) : ?>
</ul>
<?php endif ?>
