<?php $view->script('settings', 'app/system/app/settings.js', 'vue-system') ?>

<form id="js-settings" class="uk-form uk-form-horizontal" v-cloak v-on="submit: save">

    <?php $view->section()->start('toolbar') ?>
        <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>
        <a class="uk-button" v-attr="href: $url('admin/system')">{{ 'Close' | trans }}</a>
    <?php $view->section()->stop(true) ?>

    <div class="uk-grid uk-grid-divider" data-uk-grid-margin data-uk-grid-match>

        <div class="uk-width-medium-1-4 pk-sidebar-left">

            <div class="uk-panel pk-panel-marginless">
                <ul class="uk-nav uk-nav-side" data-uk-tab="{ connect: '#tab-content' }">
                    <?php foreach($sections as $section) : ?>
                    <li><a>{{ '<?= $section['label'] ?>' | trans }}</a></li>
                    <?php endforeach ?>
                </ul>
            </div>

        </div>
        <div class="uk-width-medium-3-4">

            <ul id="tab-content" class="uk-switcher uk-margin">

                <?php foreach ($sections as $section) : ?>
                <li><?= $section['view'] ?></li>
                <?php endforeach ?>

            </ul>

        </div>

    </div>

</form>
