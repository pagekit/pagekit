<form class="uk-form uk-form-horizontal" action="<?= $view->url('@dashboard/save', ['id' => $widget->getId()]) ?>" method="post">

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <h2 class="uk-margin-remove">{{ 'Edit Widget' | trans }}</h2>

        </div>
        <div data-uk-margin>

            <button class="uk-button uk-button-primary" type="submit"><?= __('Save') ?></button>
            <a class="uk-button" href="<?= $view->url('@dashboard/settings') ?>"><?= $widget->getId() ? __('Close') : __('Cancel') ?></a>

        </div>
    </div>

    <?= $type->renderForm($widget) ?>

    <input type="hidden" name="widget[type]" value="<?= $type->getId() ?>">
    <?php $view->token()->get() ?>

</form>
