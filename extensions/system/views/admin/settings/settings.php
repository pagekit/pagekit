<?php $view->script('settings', 'extensions/system/app/settings.js', 'vue-system') ?>

<form id="js-settings" class="uk-form uk-form-horizontal" v-cloak v-on="submit: save">

    <?php $app['sections']->start('toolbar', 'show') ?>
        <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>
        <a class="uk-button" v-attr="href: $url('admin/system')">{{ 'Close' | trans }}</a>
    <?php $app['sections']->end() ?>

    <div class="uk-grid uk-grid-divider" data-uk-grid-margin data-uk-grid-match>

        <div class="uk-width-medium-1-4 pk-sidebar-left">

            <div class="uk-panel pk-panel-marginless">
                <ul class="uk-nav uk-nav-side" data-uk-tab="{ connect: '#tab-content' }">
                    <?php foreach($views as $view) : ?>
                    <li><a>{{ '<?= $view['label'] ?>' | trans }}</a></li>
                    <?php endforeach ?>
                </ul>
            </div>

        </div>
        <div class="uk-width-medium-3-4">

            <ul id="tab-content" class="uk-switcher uk-margin">

                <?php foreach ($views as $view) : ?>
                <li><?= $view['view'] ?></li>
                <?php endforeach ?>

            </ul>

        </div>

    </div>

</form>
