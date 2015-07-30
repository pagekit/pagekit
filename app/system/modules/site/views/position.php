<?php foreach ($widgets as $widget) : ?>
<div class="<?= $widget->get('html_class') ?>">

    <?php if (!$widget->get('title_hide')) : ?>
    <h3><?= $widget->title ?></h3>
    <?php endif ?>

    <?= $widget->get('result') ?>

</div>
<?php endforeach ?>
