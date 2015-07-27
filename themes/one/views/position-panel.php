<?php foreach ($widgets as $widget) : ?>
<div class="uk-panel <?= $widget->get('theme.panel') ?><?= $widget->get('theme.alignment') ? ' uk-text-center' : '' ?>">

    <?php if ($widget->get('theme.badge.text')) : ?>
    <div class="<?= $widget->get('theme.badge.type') ?>"><?= $widget->get('theme.badge.text') ?></div>
    <?php endif ?>

    <?php if (!$widget->get('title_hide')) : ?>
    <h3><?= $widget->getTitle() ?></h3>
    <?php endif ?>

    <?= $widget->get('result') ?>

</div>
<?php endforeach ?>
