<?php if ($root->getDepth() === 0) : ?>
<ul class="uk-nav<?= ' '.implode(' ', (array) @$class) ?>">
<?php endif ?>

    <?php foreach ($root->getChildren() as $node) : ?>
    <li class="<?= $node->hasChildren() ? 'uk-parent' : '' ?><?= $node->get('active') ? ' uk-active' : '' ?>">
        <a href="<?= $node->getUrl() ?>"><?= $node->title ?></a>

        <?php if ($node->hasChildren()) : ?>
        <ul class="uk-nav-sub">
            <?= $view->render('system/site/menu.php', ['root' => $node]) ?>
        </ul>
        <?php endif ?>
    </li>
    <?php endforeach ?>

<?php if ($root->getDepth() === 0) : ?>
</ul>
<?php endif ?>
