<?php $this['scripts']->queue('dashboard', 'extensions/system/modules/dashboard/assets/js/settings.js', 'requirejs') ?>

<form id="js-dashboard" class="uk-form" action="<?= $app['url']->get('@system/admin') ?>" method="post" data-reorder="<?= $app['url']->get('@system/dashboard/reorder') ?>">

   <?php $this['sections']->start('toolbar', 'show') ?>

        <div class="uk-button-dropdown" data-uk-dropdown="{ mode: 'click' }">
            <button class="uk-button uk-button-primary" type="button"><?= __('Add Widget') ?></button>
            <div class="uk-dropdown uk-dropdown-small">
                <ul class="uk-nav uk-nav-dropdown">
                    <?php foreach ($types as $type): ?>
                    <li><a href="<?= $app['url']->get('@system/dashboard/add', ['type' => $type->getId()]) ?>"><?= $type->getName() ?></a></li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>

        <a class="uk-button pk-button-danger uk-hidden js-show-on-select" href="#" data-action="<?= $app['url']->get('@system/dashboard/delete') ?>"><?= __('Delete') ?></a>

    <?php $this['sections']->end() ?>

    <div class="pk-table-fake pk-table-fake-header pk-table-fake-header-indent">
        <div class="pk-table-width-minimum"><input type="checkbox" class="js-select-all"></div>
        <div><?= __('Widget') ?></div>
        <div class="pk-table-width-100"><?= __('Type') ?></div>
    </div>

    <ul class="uk-nestable" data-uk-nestable="{ maxDepth: 1 }">
        <?php foreach ($widgets as $id => $widget): ?>
        <li data-id="<?= $id ?>">

            <div class="uk-nestable-item pk-table-fake">
                <div class="pk-table-width-minimum"><div class="uk-nestable-handle">​</div></div>
                <div class="pk-table-width-minimum"><input class="js-select" type="checkbox" name="ids[]" value="<?= $id ?>"></div>
                <div>
                    <a href="<?= $app['url']->get('@system/dashboard/edit', ['id' => $id]) ?>"><?= $widget->getTitle() ?></a>
                </div>
                <div class="pk-table-width-100"><?= $widget->getType() ?></div>
            </div>

        </li>
        <?php endforeach ?>
    </ul>

    <?php $this['token']->generate() ?>

</form>
