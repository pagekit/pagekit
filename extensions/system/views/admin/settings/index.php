<?php $view->style('system', 'extensions/system/assets/css/system.css') ?>
<?php $view->script('settings', 'extensions/system/assets/js/settings/index.js', 'uikit') ?>

<h2 class="uk-h3"><?= __('System') ?></h2>

<ul class="uk-grid pk-system" data-uk-grid-margin>

    <?php if ($user->hasAccess('system: access settings')): ?>
    <li class="uk-width">
        <a class="uk-panel pk-panel-icon" href="<?= $view->url('@system/settings') ?>">
            <img src="<?= $view->url()->getStatic('extensions/system/assets/images/icon-settings.svg') ?>" width="50" height="50" alt="<?= __('Settings') ?>">
            <p><?= __('Settings') ?></p>
        </a>
    </li>
    <?php endif ?>

    <?php if ($user->hasAccess('system: manage extensions')): ?>
    <li class="uk-width">
        <a class="uk-panel pk-panel-icon" href="<?= $view->url('@system/extensions') ?>">
            <img src="<?= $view->url()->getStatic('extensions/system/assets/images/icon-extensions.svg') ?>" width="50" height="50" alt="<?= __('Extensions') ?>">
            <p><?= __('Extensions') ?></p>
        </a>
    </li>
    <?php endif ?>

    <?php if ($user->hasAccess('system: manage themes')): ?>
    <li class="uk-width">
        <a class="uk-panel pk-panel-icon" href="<?= $view->url('@system/themes') ?>">
            <img src="<?= $view->url()->getStatic('extensions/system/assets/images/icon-themes.svg') ?>" width="50" height="50" alt="<?= __('Themes') ?>">
            <p><?= __('Themes') ?></p>
        </a>
    </li>
    <?php endif ?>

    <li class="uk-width">
        <a class="uk-panel pk-panel-icon" href="<?= $view->url('@system/dashboard/settings') ?>">
            <img src="<?= $view->url()->getStatic('extensions/system/assets/images/icon-dashboard.svg') ?>" width="50" height="50" alt="<?= __('Dashboard') ?>">
            <p><?= __('Dashboard') ?></p>
        </a>
    </li>

    <?php if ($user->hasAccess('system: manage storage')): ?>
    <li class="uk-width">
        <a class="uk-panel pk-panel-icon" href="<?= $view->url('@system/system/storage') ?>">
            <img src="<?= $view->url()->getStatic('extensions/system/assets/images/icon-storage.svg') ?>" width="50" height="50" alt="<?= __('Storage') ?>">
            <p><?= __('Storage') ?></p>
        </a>
    </li>
    <?php endif ?>

    <?php if ($user->hasAccess('system: software updates')): ?>
    <li class="uk-width">
        <a class="uk-panel pk-panel-icon" href="<?= $view->url('@system/update') ?>">
            <img src="<?= $view->url()->getStatic('extensions/system/assets/images/icon-update.svg') ?>" width="50" height="50" alt="<?= __('Update') ?>">
            <p><?= __('Update') ?></p>
        </a>
    </li>
    <?php endif ?>

    <li class="uk-width">
        <a class="uk-panel pk-panel-icon" href="<?= $view->url('@system/system/info') ?>">
            <img src="<?= $view->url()->getStatic('extensions/system/assets/images/icon-info.svg') ?>" width="50" height="50" alt="<?= __('Info') ?>">
            <p><?= __('Info') ?></p>
        </a>
    </li>

    <li class="uk-width">
        <a id="clearCache" class="uk-panel pk-panel-icon">
            <img src="<?= $view->url()->getStatic('extensions/system/assets/images/icon-cache.svg') ?>" width="50" height="50" alt="<?= __('Clear Cache') ?>">
            <p><?= __('Clear Cache') ?></p>
        </a>
    </li>

</ul>

<?php if ($packages && $user->hasAccess('system: manage extensions')): ?>
<hr class="uk-margin-large">

<h2 class="uk-h3"><?= __('Extensions') ?></h2>

<ul class="uk-grid pk-system" data-uk-grid-margin>

    <?php foreach ($packages as $extension => $package): ?>
    <li class="uk-width">
        <a class="uk-panel pk-panel-icon" href="<?= $view->url('@system/extensions/settings', ['name' => $extension]) ?>">
            <img class="uk-img-preserve" src="<?= $view->url('extensions/system/assets/images/placeholder-icon.svg') ?>" width="50" height="50" alt="<?= $package->getTitle() ?>">
            <p><?= $package->getTitle() ?></p>
        </a>
    </li>
    <?php endforeach ?>

</ul>
<?php endif ?>

<div id="modal-clearcache" class="uk-modal">
    <div class="uk-modal-dialog">

        <h4><?= __('Select caches to clear:') ?></h4>

        <form class="uk-form" action="<?= $view->url('@system/cache/clear') ?>" method="post">

            <div class="uk-form-row">
                <div class="uk-form-controls uk-form-controls-text">
                    <p class="uk-form-controls-condensed">
                        <label><input type="checkbox" name="caches[cache]" value="1" checked> <?= __('System Cache') ?></label>
                    </p>
                </div>
            </div>
            <div class="uk-form-row">
                <div class="uk-form-controls uk-form-controls-text">
                    <p class="uk-form-controls-condensed">
                        <label><input type="checkbox" name="caches[temp]" value="1"> <?= __('Temporary Files') ?></label>
                    </p>
                </div>
            </div>
            <p>
                <button class="uk-button uk-button-primary" type="submit"><?= __('Clear') ?></button>
                <button class="uk-button uk-modal-close" type="submit"><?= __('Cancel') ?></button>
            </p>

        </form>

    </div>
</div>
