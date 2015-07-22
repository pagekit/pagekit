<?php if ($root->getDepth() === 0) : ?>
<ul class="uk-navbar-nav">
    <?php endif ?>

    <?php foreach ($root->getChildren() as $node) : ?>

        <li class="<?= $node->hasChildren() ? 'uk-parent' : '' ?><?= $node->get('active') ? ' uk-active' : '' ?>" <?= ($root->getDepth() === 0 && $node->hasChildren()) ? 'data-uk-dropdown':'' ?>>
            <a href="<?= $node->getUrl() ?>"><?= $node->getTitle() ?></a>

            <?php if ($root->getDepth() === 0 && $node->hasChildren()) : ?>
            <div class="uk-dropdown uk-dropdown-navbar">
            <?php endif ?>

            <?php if ($node->hasChildren()) : ?>
                <ul class="uk-nav uk-nav-navbar">
                    <?= $view->render('system/site/menu.php', ['root' => $node]) ?>
                </ul>
            <?php endif ?>

            <?php if ($root->getDepth() === 1 && $node->hasChildren()) : ?>
            </div>
            <?php endif ?>

        </li>

    <?php endforeach ?>

    <?php if ($root->getDepth() === 0) : ?>
</ul>
<?php endif ?>
