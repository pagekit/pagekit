<?php if ($root->getDepth() === 0) : ?>
<ul class="uk-nav>
<?php endif ?>

<?php foreach ($root->getChildren() as $node) : ?>

    <li>
        <a href="<?= 'TEST' ?>"><?= $node->getTitle() ?></a>
        <?php if ($node->hasChildren() && ($node->get('active') || $widget->get('mode', 'all') == 'all' || !$root->getDepth()) == 0) : ?>
        <ul class="uk-nav-sub">
            <?= $this->render('menu', ['root' => $node, 'widget' => $widget]) ?>
        </ul>
        <?php endif ?>
    </li>

<?php endforeach ?>

<?php if ($root->getDepth() === 0) : ?>
    </ul>
<?php endif ?>
