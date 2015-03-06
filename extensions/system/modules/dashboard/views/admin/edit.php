<form class="uk-form uk-form-horizontal" action="<?= $app['url']->get('@system/dashboard/save', ['id' => $widget->getId()]) ?>" method="post">

    <?php $app['sections']->start('toolbar', 'show') ?>
        <button class="uk-button uk-button-primary" type="submit"><?= __('Save') ?></button>
        <a class="uk-button" href="<?= $app['url']->get('@system/dashboard/settings') ?>"><?= $widget->getId() ? __('Close') : __('Cancel') ?></a>
    <?php $app['sections']->end() ?>

    <?= $type->renderForm($widget) ?>

    <input type="hidden" name="widget[type]" value="<?= $type->getId() ?>">
    <?php $this['token']->generate() ?>

</form>