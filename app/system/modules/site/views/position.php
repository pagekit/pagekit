<?php foreach ($widgets as $widget) : ?>
<div class="<?= $widget->get('class') ?>">

    <?php if ($widget->get('show_title')) : ?>
    <h3><?= $widget->getTitle() ?></h3>
    <?php endif ?>

    <?= $widget->get('result') ?>

</div>
<?php endforeach ?>