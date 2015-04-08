<?php $view->script('settings', 'app/system/modules/settings/app/settings.js', 'vue-system') ?>

<form id="settings" class="uk-form uk-form-horizontal" v-cloak v-on="submit: save">

    <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
        <div data-uk-margin>

            <h2 class="uk-margin-remove">{{ 'Settings' | trans }}</h2>

        </div>
        <div data-uk-margin>

            <button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

        </div>
    </div>

    <div class="uk-grid" data-uk-grid-margin>

        <div class="uk-width-medium-1-4">

            <div class="uk-panel">
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
