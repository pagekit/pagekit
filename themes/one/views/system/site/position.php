<?php foreach ($widgets as $widget) : ?>
<div class="<?= $widget->theme['html_class'] ?>">

    <?php if (!$widget->theme['title_hide']) : ?>
    <h3><?= $widget->title ?></h3>
    <?php endif ?>

    <?= $widget->get('result') ?>

</div>
<?php endforeach ?>
