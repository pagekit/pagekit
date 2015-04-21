<?php $view->style('system', 'app/system/assets/css/system.css') ?>

<div class="uk-margin uk-clearfix">
    <a  class="uk-float-right uk-icon-hover uk-icon-small uk-icon-cog" href="<?= $view->url('@dashboard/settings') ?>" title="<?= __('Dashboard Settings') ?>"></a>
</div>

<div class="uk-grid pk-grid-small uk-grid-medium" data-uk-grid-margin>

    <?php foreach ($columns as $column): ?>
    <div class="uk-width-medium-1-3">
        <?php foreach ($column as $id): ?>
        <div class="uk-panel uk-panel-box">
            <?= $widgets[$id] ?>
        </div>
        <?php endforeach ?>
    </div>
    <?php endforeach ?>

</div>
