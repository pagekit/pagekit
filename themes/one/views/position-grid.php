<?php foreach ($widgets as $widget) : ?>
<div class="uk-width-medium-1-<?= count($widgets) ?>">

    <div class="uk-panel <?= $widget->theme['panel'] ?><?= $widget->theme['alignment'] ? ' uk-text-center' : '' ?>">

        <?php if (!$widget->theme['title_hide']) : ?>
        <h3 class="<?= $widget->theme['title_size'] ?>"><?= $widget->title ?></h3>
        <?php endif ?>

        <?= $widget->get('result') ?>

    </div>

</div>
<?php endforeach ?>
